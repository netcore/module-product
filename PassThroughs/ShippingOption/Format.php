<?php

namespace Modules\Product\PassThroughs\ShippingOption;

use Modules\Product\Models\ShippingOption;
use Modules\Product\Models\ShippingOptionLocation;
use Modules\Product\PassThroughs\PassThrough;
use Netcore\Translator\Models\Language;

class Format extends PassThrough
{
    /**
     * ShippingOption model instance.
     *
     * @var \Modules\Product\Models\ShippingOption
     */
    protected $shippingOption;

    /**
     * Format constructor.
     *
     * @param \Modules\Product\Models\ShippingOption $shippingOption
     * @return void
     */
    public function __construct(ShippingOption $shippingOption)
    {
        $this->shippingOption = $shippingOption;
    }

    /**
     * Format shipping option for client side.
     *
     * @return array
     */
    public function forClientSide(): array
    {
        $data = [
            'id'    => $this->shippingOption->id,
            'name'  => $this->shippingOption->name,
            'price' => $this->shippingOption->price,
        ];

        if ($this->shippingOption->locations->count()) {
            $data['locations'] = $this->shippingOption->locations->map(function (ShippingOptionLocation $location) {
                return [
                    'id'   => $location->id,
                    'name' => $location->name . ', ' . $location->address,
                ];
            });
        }

        return $data;
    }

    public function forAdminSide(): array
    {
        $translations = languages()->mapWithKeys(function (Language $language) {
            return [$language->iso_code => ['name' => optional($this->shippingOption->translate($language->iso_code))->name]];
        });

        $locations = $this->shippingOption->locations->map(function (ShippingOptionLocation $location) {
            return array_only($location->toArray(), ['id', 'name', 'address', 'city', 'zip', 'longitude', 'latitude']);
        });

        return [
            'id'              => $this->shippingOption->id,
            'price'           => $this->shippingOption->price,
            'price_when_free' => $this->shippingOption->price_when_free,
            'type'            => $this->shippingOption->type,
            'translations'    => $translations,
            'locations'       => $locations,
        ];
    }
}