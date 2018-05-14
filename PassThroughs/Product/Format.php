<?php

namespace Modules\Product\PassThroughs\Product;

use Illuminate\Support\Collection;
use Modules\Product\Models\Field;
use Modules\Product\Models\Parameter;
use Modules\Product\Models\Product;
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
        $this->product->load('enabledParameters', 'enabledAttributes');

        $data = [
            'id'           => $this->product->id,
            'is_variable'  => $this->product->is_variable,
            'categories'   => $this->product->categories->pluck('id'),
            'variants'     => [],
            'fieldsData'   => [],
            'images'       => $this->formatImagesForVue($this->product),
            'prices'       => $this->formatPricesForVue($this->product),
            'parameters'   => $this->formatParametersForVue($this->product),
            'translations' => $this->formatTranslationsForVue($this->product),

            'uploadableImages' => [],
        ];

        $fieldsOfCategories = app(FieldsRepository::class)->getFieldsOfCategories($this->product->categories);

        // Product fields.
        $data['fieldsData'] = $fieldsOfCategories->map(function (Field $field) {
            return $this->formatProductFieldForVue($field);
        });

        // Product variants.
        if ($this->product->is_variable) {
            foreach ($this->product->children as $child) {
                $variant = [
                    'id'           => $child->id,
                    'key'          => str_random(12),
                    'translations' => $this->formatTranslationsForVue($child),
                    'prices'       => $this->formatPricesForVue($child),
                    'parameters'   => $this->formatParametersForVue($child),
                    'images'       => $this->formatImagesForVue($child),

                    'uploadableImages' => [],
                ];

                $data['variants'][] = $variant;
            }
        }

        return $data;
    }

    /**
     * Get product images for admin side VueJS.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Support\Collection
     */
    private function formatImagesForVue(Product $product): Collection
    {
        return $product->images->map(function (ProductImage $image) {
            return [
                'id'         => $image->id,
                'size'       => $image->humanReadableImageSize,
                'name'       => $image->image_file_name,
                'image'      => $image->image->url('thumb'),
                'modified'   => $image->image_updated_at,
                'is_preview' => $image->is_preview,
            ];
        });
    }

    /**
     * Format product parameters for admin side VueJS.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Support\Collection
     */
    private function formatParametersForVue(Product $product): Collection
    {
        return $product->enabledParameters->mapWithKeys(function (Parameter $parameter) use ($product) {
            $data = [
                'enabled' => true,
            ];

            if ($parameter->is_countable || $parameter->type == 'checkbox') {
                $data['attributes'] = [];

                foreach ($parameter->attributes as $attribute) {
                    $productAttribute = $product->enabledAttributes->where('id', $attribute->id)->first();

                    $data['attributes'][$attribute->id] = [
                        'enabled'  => !!$productAttribute,
                        'quantity' => $productAttribute ? $productAttribute->pivot->quantity : 0,
                    ];
                }
            } else {
                $data['value'] = $parameter->pivot->value;
            }

            return [$parameter->id => $data];
        });
    }

    /**
     * Format product field, fill with existing values for admin side VueJS.
     *
     * @param \Modules\Product\Models\Field $field
     * @return array
     */
    private function formatProductFieldForVue(Field $field): array
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
                $fieldData['value'] = $productField->value ?? null;
            }
        }

        return $fieldData;
    }

    /**
     * Format product prices for admin side VueJS.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Support\Collection
     */
    private function formatPricesForVue(Product $product): Collection
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
     * Format translations for admin side VueJS.
     *
     * @param \Modules\Product\Models\Product $product
     * @return array
     */
    private function formatTranslationsForVue(Product $product)
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