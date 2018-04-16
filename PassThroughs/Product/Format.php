<?php

namespace Modules\Product\PassThroughs\Product;

use Illuminate\Support\Collection;
use Modules\Product\Models\Field;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductField;
use Modules\Product\Models\ProductFieldTranslation;
use Modules\Product\Models\ProductImage;
use Modules\Product\Models\ProductPrice;
use Modules\Product\PassThroughs\PassThrough;
use Modules\Product\Repositories\FieldsRepository;

class Format extends PassThrough
{
    /**
     * Product model.
     *
     * @var \Modules\Product\Models\Product
     */
    protected $product;

    /**
     * Format constructor.
     *
     * @param \Modules\Product\Models\Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Format data for admin side VueJs app.
     *
     * @return array
     */
    public function forAdminSideVue()
    {
        $data = [
            'id'           => $this->product->id,
            'is_variable'  => $this->product->is_variable,
            'categories'   => $this->product->categories->pluck('id'),
            'images'       => $this->product->images->sortBy('order')->map->formatForResponse()->values(),
            'variants'     => [],
            'fieldsData'   => [],
            'prices'       => $this->formatPrices($this->product),
            'translations' => $this->formatTranslations($this->product),
        ];

        // Product fields.
        $fieldsData = collect();

        app(FieldsRepository::class)
            ->getFieldsOfCategories($this->product->categories)
            ->each(function (Field $field) use ($fieldsData) {
                $this->formatProductField($field, $fieldsData);
            });

        $data['fieldsData'] = $fieldsData;

        // Product variants.
        if ($this->product->is_variable) {
            foreach ($this->product->children as $child) {
                $variant = [
                    'id'           => $child->id,
                    'key'          => str_random(12),
                    'translations' => $this->formatTranslations($child),
                    'prices'       => $this->formatPrices($child),
                    'images'       => $child->images->sortBy('order')->map->formatForResponse()->values(),
                ];

                $data['variants'][] = $variant;
            }
        }

        return $data;
    }

    /**
     * Format product field, fill with existing value.
     *
     * @param \Modules\Product\Models\Field $field
     * @param \Illuminate\Support\Collection $fieldsData
     */
    private function formatProductField(Field $field, Collection &$fieldsData)
    {
        $productField = $this->product->productFields->where('field_id', $field->id)->first();

        if ($field->is_translatable) {
            $translations = [];

            foreach (languages() as $language) {
                $translationObject = $productField->translations->where('locale', $language->iso_code)->first();

                $translations[$language->iso_code] = [
                    'value' => $translationObject->value ?? '',
                ];
            }

            $value = ['translations' => $translations];
        } else {
            if ($field->type == 'checkbox') {
                $value = ['value' => (bool)$productField->value ?? ''];
            } else {
                $value = ['value' => $productField->value ?? ''];
            }
        }

        $fieldsData->put($field->id, $value);
    }

    private function formatPrices(Product $product): Collection
    {
        return $product->prices->mapWithKeys(function (ProductPrice $price) {
            return [
                $price->currency_id => [
                    'discount_amount' => (float)$price->discount_amount,
                    'discount_type'   => $price->discount_type,
                    'has_discount'    => $price->discount_type != 'none',

                    'with_vat_with_discount'       => (float)$price->with_vat_with_discount,
                    'with_vat_without_discount'    => (float)$price->with_vat_without_discount,
                    'without_vat_with_discount'    => (float)$price->without_vat_with_discount,
                    'without_vat_without_discount' => (float)$price->without_vat_without_discount,
                ],
            ];
        });
    }

    private function formatTranslations(Product $product)
    {
        static $languages;

        if (! $languages) {
            $languages = languages();
        }

        $translations = [];

        foreach ($languages as $language) {
            $existingTranslation = $product->translations->where('locale', $language->iso_code)->first();
            $translations[$language->iso_code] = [];

            foreach ($product->translatedAttributes as $translatedAttribute) {
                $value = optional($existingTranslation)->{$translatedAttribute};
                $translations[$language->iso_code][$translatedAttribute] = $value ?? '';
            }
        }

        return $translations;
    }
}