<?php

namespace Modules\Product\PassThroughs\Product;

use Illuminate\Support\Collection;
use Modules\Product\Models\Field;
use Modules\Product\Models\Product;
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

            'uploadableImages' => [],
        ];

        $fieldsOfCategories = app(FieldsRepository::class)->getFieldsOfCategories($this->product->categories);

        // Product fields.
        $data['fieldsData'] = $fieldsOfCategories->map(function (Field $field) {
            return $this->formatProductField($field);
        });

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
     * @return array
     */
    private function formatProductField(Field $field): array
    {
        $productField = $this->product->productFields->where('field_id', $field->id)->first();

        $fieldData = [
            'id' => $field->id,
        ];

        if ($field->is_translatable) {
            $translations = [];

            foreach (languages() as $language) {
                $translationObject = $productField->translations->where('locale', $language->iso_code)->first();
                $translations[$language->iso_code] = $translationObject->value ?? '';
            }

            $fieldData['values'] = $translations;
        } else {
            if ($field->type == 'checkbox') {
                $fieldData['value'] = (bool)$productField->value ?? 0;
            } else {
                $fieldData['value'] = $productField->value;
            }
        }

        return $fieldData;
    }

    /**
     * Format product prices.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * Format translations.
     *
     * @param \Modules\Product\Models\Product $product
     * @return array
     */
    private function formatTranslations(Product $product)
    {
        static $languages;

        if (!$languages) {
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