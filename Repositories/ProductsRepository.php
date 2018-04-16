<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Modules\Country\Models\Country;
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
            $data['fields'] ?? []
        );

        // Prices.
        $product->management()->syncPrices(
            $data['prices'] ?? []
        );

        // Translations.
        $product->syncTranslations(
            $data['translations'] ?? []
        );

        // Images.
        foreach ($data['images'] ?? [] as $image) {
            $product->images()->create([
                'image' => $image,
            ]);
        }

        if ($product->is_variable) {
            foreach ($data['variants'] ?? [] as $variant) {
                if (isset($variant['id']) && !is_null($variant['id'])) {
                    $productVariant = $product->children()->findOrFail($variant['id']);
                } else {
                    $productVariant = $product->children()->create([]);
                }

                $this->updateProduct($productVariant, $variant);
            }
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
}