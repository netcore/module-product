<?php

namespace Modules\Product\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryGroup;
use Modules\Country\Models\Country;
use Modules\Product\Mappers\Mapper;
use Modules\Product\Models\Parameter;
use Modules\Product\Models\ParameterAttribute;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSetting;

class ProductsRepository
{
    /**
     * Update or create product.
     *
     * @param \Modules\Product\Models\Product $product
     * @param array $data
     */
    public function updateProduct(Product $product, array $data)
    {
        // Sync categories.
        $product->categories()->sync(
            $data['categories'] ?? []
        );

        // Sync product fields.
        $product->management()->syncFields(
            $data['fieldsData'] ?? []
        );

        // Product variants.
        if ($product->is_variable) {
            foreach ($data['variants'] ?? [] as $variant) {
                if (isset($variant['id']) && !is_null($variant['id'])) {
                    $productVariant = $product->children()->findOrFail($variant['id']);
                } else {
                    $productVariant = $product->children()->create([]);
                }

                $this->updateProduct($productVariant, $variant);
            }

            return;
        }

        // Prices.
        $product->management()->syncPrices(
            $data['prices'] ?? []
        );

        // Parameters.
        $product->management()->syncParameters(
            $data['parameters'] ?? []
        );

        // Translations.
        $product->syncTranslations(
            $data['translations'] ?? []
        );

        // Images.
        foreach ($data['uploadableImages'] ?? [] as $image) {
            if (!isset($image['file']) || !$image['file'] instanceof UploadedFile) {
                continue;
            }

            $product->images()->create([
                'image' => $image['file'],
            ]);
        }
    }

    /**
     * Get enabled currencies with vat percent.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrencies(): Collection
    {
        $settings = ProductSetting::getCachedSettings();

        return country()->all()->map(function (Country $country) use ($settings) {
            $setting = $settings
                ->where('configurable_id', $country->currency->id)
                ->where('configurable_type', get_class($country->currency))
                ->where('key', 'vat')
                ->first();

            return new Fluent([
                'id'     => $country->currency->id,
                'code'   => $country->currency->code,
                'symbol' => $country->currency->symbol,
                'vat'    => $setting->value ?? 21,
            ]);
        });
    }

    /**
     * Get current currency.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function getCurrency(): Fluent
    {
        $currencies = $this->getCurrencies();
        $currentCurrency = session('currency');

        if ($currentCurrency && $currency = $currencies->where('id', session('currency'))->first()) {
            return $currency;
        }

        return $currencies->first();
    }

    /**
     * Get the product module associated category group.
     *
     * @return \Modules\Category\Models\CategoryGroup
     */
    public function getCategoryGroup(): CategoryGroup
    {
        static $categoryGroup;

        if (!$categoryGroup) {
            $categoryGroup = CategoryGroup::where([
                'key' => config('netcore.module-product.used_category_group.key'),
            ])->first();

            if (!$categoryGroup) {
                $categoryGroup = new CategoryGroup;
            }
        }

        return $categoryGroup;
    }

    /**
     * Paginate products.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Category\Models\Category|null $category
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(Request $request, ?Category $category)
    {
        $perPage = $request->query('perPage', 20);

        if (app()->environment() !== 'production') {
            \Debugbar::startMeasure('ProductFilterQuery');
        }

        $query = Product::query();
        $query->where('is_variable', false);

        $categoryDescendantsAndSelf = null;

        // Is in given category.
        if ($category) {
            $categoryDescendantsAndSelf = Category::descendantsAndSelf($category->id)->pluck('id')->toArray();

            $query->where(function (Builder $categorySubQuery) use ($categoryDescendantsAndSelf) {
                $categorySubQuery->whereHas('categories', function (Builder $subQuery) use ($categoryDescendantsAndSelf) {
                    return $subQuery->whereIn('id', $categoryDescendantsAndSelf);
                });

                $categorySubQuery->orWhereHas('parent.categories', function (Builder $subQuery) use ($categoryDescendantsAndSelf) {
                    return $subQuery->whereIn('id', $categoryDescendantsAndSelf);
                });
            });
        }

        // Parameters and attributes.
        // @TODO - rebuild data array in frontend to filter product per parameter group, not just plain attributes.

        $checkboxes = $request->input('filters.checkboxes', []);
        $ranges = $request->input('filters.ranges', []);

        if (count($checkboxes)) {
            $query->where(function (Builder $builder) use ($checkboxes) {
                $i = 0;

                foreach ($checkboxes as $id => $value) {
                    if ($value == 'false') {
                        continue;
                    }

                    $builder->{$i ? 'orWhereHas' : 'whereHas'}('enabledAttributes', function (Builder $subQuery) use ($id) {
                        return $subQuery->where('id', $id);
                    });

                    $i++;
                }

                return $builder;
            });
        }

        if (count($ranges)) {
            $nonRangeParameters = Parameter::with('attributes.translations')->whereDisplayAsRange(true)->where('type', '<>', 'range')->get()->keyBy('id');

            $query->where(function (Builder $builder) use ($ranges, $nonRangeParameters) {
                $i = 0;

                foreach ($ranges as $id => $value) {
                    if (!is_array($value) || !isset($value['from']) || !isset($value['to'])) {
                        continue;
                    }

                    $fromTo = range($value['from'], $value['to']);

                    if (!$nonRangeParameters->has($id)) {
                        $builder->{$i ? 'orWhereHas' : 'whereHas'}('enabledParameters', function (Builder $subQuery) use ($id, $fromTo) {
                            // ->whereBetween is not working in this case, because we are dealing with string values in the database.
                            return $subQuery->where('id', $id)->whereIn('value', $fromTo);
                        });
                    } else {
                        $attributeIds = $nonRangeParameters->get($id)->attributes->filter(function (ParameterAttribute $attribute) use ($fromTo) {
                            return in_array($attribute->name, $fromTo);
                        })->pluck('id')->toArray();

                        $builder->where(function (Builder $subQuery) use ($id, $attributeIds) {
                            return $subQuery
                                ->whereHas('enabledParameters', function (Builder $parameterQuery) use ($id) {
                                    return $parameterQuery->where('id', $id);
                                })
                                ->whereHas('enabledAttributes', function (Builder $attributeQuery) use ($attributeIds) {
                                    return $attributeQuery->whereIn('id', $attributeIds);
                                });
                        });
                    }

                    $i++;
                }

                return $builder;
            });
        }

        // Search by keyword.
        if ($term = $request->query('search')) {
            $query->whereHas('translations', function (Builder $query) use ($term) {
                return $query->where('title', 'LIKE', '%' . $term . '%');
            });
        }

        // Sort by.
        $sortOptions = ['date', 'popularity', 'price_desc', 'price_asc'];
        $sortBy = $request->query('sort', 'date');

        if (in_array($sortBy, $sortOptions)) {

            // Join price to enable sorting by relation.
            if (strstr($sortBy, 'price_')) {
                $query->join('netcore_product__product_prices as price', function (JoinClause $joinClause) {
                    $currency = product()->getCurrency();

                    $joinClause->on('netcore_product__products.id', '=', 'price.product_id')->where('price.currency_id', $currency->id);
                });
            }

            switch ($sortBy) {
                case 'date':
                    $query->orderBy('created_at', 'DESC');
                    break;

                case 'popularity':
                    $query->orderBy('average_rating', 'DESC');
                    break;

                case 'price_desc':
                    $query->orderBy('price.with_vat_with_discount', 'DESC');
                    break;

                case 'price_asc':
                    $query->orderBy('price.with_vat_with_discount', 'ASC');
                    break;
            }
        }

        if (app()->environment() !== 'production') {
            \Debugbar::stopMeasure('ProductFilterQuery');
        }

        return $query->paginate($perPage);
    }
}