<?php

namespace Modules\Product\PassThroughs\Product;

use Modules\Product\Models\Parameter;
use Modules\Product\Models\Product;
use Modules\Product\PassThroughs\PassThrough;
use Modules\Product\Repositories\FieldsRepository;

class Management extends PassThrough
{
    /**
     * Product model.
     *
     * @var \Modules\Product\Models\Product
     */
    protected $product;

    /**
     * Fields repository.
     *
     * @var \Modules\Product\Repositories\FieldsRepository
     */
    protected $fieldsRepository;

    /**
     * Management constructor.
     *
     * @param \Modules\Product\Models\Product $product
     */
    public function __construct(Product $product)
    {
        $this->fieldsRepository = new FieldsRepository;
        $this->product = $product;
    }

    /**
     * Sync product fields.
     *
     * @param array $fieldsData
     * @return void
     */
    public function syncFields(array $fieldsData): void
    {
        $fields = $this->fieldsRepository->getFieldsOfCategories(
            $this->product->categories
        );

        $fieldsFromFrontend = collect($fieldsData);

        foreach ($fields as $field) {
            $fieldFromFrontend = $fieldsFromFrontend->where('id', $field->id)->first();

            if (!$fieldFromFrontend) {
                continue;
            }

            $productField = $this->product->productFields()->firstOrCreate([
                'field_id' => $field->id,
            ]);

            if ($field->is_translatable) {
                $translations = [];

                foreach ($fieldFromFrontend['values'] as $iso => $value) {
                    $translations[$iso] = ['value' => $value];
                }

                if ($productField->wasRecentlyCreated) {
                    $productField->storeTranslations($translations);
                } else {
                    $productField->updateTranslations($translations);
                }

                continue;
            }

            $value = array_get($fieldFromFrontend, 'value');

            if ($field->type == 'checkbox') {
                $value = !!$value;
            }

            $productField->value = $value;
            $productField->save();
        }
    }

    /**
     * Update product prices.
     *
     * @param array $prices
     * @return void
     */
    public function syncPrices(array $prices): void
    {
        $currencies = product()->getCurrencies();

        foreach ($prices as $currencyId => $price) {
            if (!$currency = $currencies->where('id', $currencyId)->first()) {
                continue;
            }

            /**
             * Validate discount.
             */
            $hasDiscount = isset($price['has_discount']);
            $discountType = $price['discount_type'] ?? 'none';
            $discountAmount = $price['discount_amount'] ?? 0;

            if (!in_array($discountType, ['none', 'percent', 'money'])) {
                $discountType = 'none';
            }

            if ($discountAmount < 0) {
                $discountAmount = 0;
            }

            if (!$hasDiscount || $discountType == 'none' || !$discountAmount) {
                $discountType = 'none';
                $discountAmount = 0;
            }

            /**
             * Calculate prices and discounts.
             */
            $vatFull = (int)$currency->vat;
            $vatPercent = (float)$vatFull / 100;
            $vatPercentFull = (float)1 + $vatPercent;

            // Without discount.
            $withoutVatWithoutDiscount = (float)$price['without_vat_without_discount'];
            $withVatWithoutDiscount = (float)$withoutVatWithoutDiscount * $vatPercentFull;

            // With discount.
            if ($discountType == 'none') {
                $withoutVatWithDiscount = $withoutVatWithoutDiscount;
                $withVatWithDiscount = $withVatWithoutDiscount;
            } else {

                // Get discount amount.
                if ($discountType == 'money') {
                    $discountAsMoney = round($withoutVatWithoutDiscount - $discountAmount, 2);
                } else {
                    $discountAsMoney = round($withoutVatWithoutDiscount * $discountAmount / 100, 2);
                }

                $withoutVatWithDiscount = $withoutVatWithoutDiscount - $discountAsMoney;
                $withVatWithDiscount = ($withoutVatWithoutDiscount - $discountAsMoney) * $vatPercentFull;
            }

            // Update prices in DB.
            $this->product->prices()->updateOrCreate([
                'currency_id' => $currency->id,
            ], [
                'discount_type'                => $discountType,
                'discount_amount'              => $discountAmount,
                'with_vat_with_discount'       => $withVatWithDiscount,
                'with_vat_without_discount'    => $withVatWithoutDiscount,
                'without_vat_with_discount'    => $withoutVatWithDiscount,
                'without_vat_without_discount' => $withoutVatWithoutDiscount,
            ]);
        }
    }

    /**
     * Sync parameters and attributes.
     *
     * @param array $parameters
     * @return void
     */
    public function syncParameters(array $parameters): void
    {
        $this->product->enabledParameters()->detach();
        $this->product->enabledAttributes()->detach();

        $parametersToAttach = [];
        $attributesToAttach = [];

        $parametersCollection = Parameter::whereIn('id', array_keys($parameters))->get();

        foreach ($parameters as $parameterId => $parameterData) {
            // Parameter is not enabled, skip.
            if (!array_get($parameterData, 'enabled', false)) {
                continue;
            }

            // Find parameter model.
            if (!$parameter = $parametersCollection->where('id', $parameterId)->first()) {
                continue;
            }

            // Get value and attributes.
            $value = array_get($parameterData, 'value');
            $attributes = array_get($parameterData, 'attributes', []);

            // Range type parameter has one pivot record in netcore_product__parameter_product table.
            if ($parameter->type == 'range') {
                $parametersToAttach[$parameterId] = compact('value');
            }

            // Radio type parameter has one pivot record in netcore_product__parameter_product table,
            // but also has pivot record in netcore_product__attribute_product table.

            if ($parameter->type == 'radio' && !$parameter->is_countable) {
                $parametersToAttach[$parameterId] = compact('value');
                $attributesToAttach[$value] = $value;
            }

            // Checkbox type parameter has one pivot record in netcore_product__parameter_product table
            // and also has many pivot records in netcore_product__attribute_product table.
            if ($parameter->type == 'checkbox' || $parameter->is_countable) {
                $parametersToAttach[$parameterId] = $parameterId;

                foreach ($attributes as $attributeId => $attributeData) {

                    // Attribute is not enabled, skip.
                    if (!isset($attributeData['enabled']) || !$attributeData['enabled']) {
                        continue;
                    }

                    $attributesToAttach[$attributeId] = [
                        'quantity' => array_get($attributeData, 'quantity', 0),
                    ];
                }
            }
        }

        // Sync parameters and attributes in one batch.
        $this->product->enabledParameters()->sync($parametersToAttach);
        $this->product->enabledAttributes()->sync($attributesToAttach);
    }
}