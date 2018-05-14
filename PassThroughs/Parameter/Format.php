<?php

namespace Modules\Product\PassThroughs\Parameter;

use Modules\Product\Models\Parameter;
use Modules\Product\Models\Product;
use Modules\Product\PassThroughs\PassThrough;

class Format extends PassThrough
{
    /**
     * Parameter model instance.
     *
     * @var \Modules\Product\Models\Parameter
     */
    private $parameter;

    /**
     * Format constructor.
     *
     * @param \Modules\Product\Models\Parameter $parameter
     */
    public function __construct(Parameter $parameter)
    {
        $this->parameter = $parameter;
    }

    /**
     * Format data for admin side VueJs app.
     *
     * @return \Illuminate\Support\Collection
     */
    public function forAdminSideVue()
    {
        $data = [
            'type'       => $this->parameter->type,
            'value_from' => $this->parameter->value_from,
            'value_to'   => $this->parameter->value_to,
            'postfix'    => $this->parameter->postfix,

            'attributes'   => [],
            'translations' => [],
            'categories'   => $this->parameter->categories->pluck('id'),

            'is_countable'        => boolval($this->parameter->is_countable),
            'iconable_attributes' => boolval($this->parameter->iconable_attributes),
            'display_as_range'    => boolval($this->parameter->display_as_range),
            'display_as_detail'   => boolval($this->parameter->display_as_detail),
        ];

        // Map parameter translations.
        foreach (languages() as $language) {
            $data['translations'][$language->iso_code] = [
                'name' => $this->parameter->translate($language->iso_code)->name ?? '',
            ];
        }

        // Map attributes.
        foreach ($this->parameter->attributes as $attribute) {
            $attributeData = [
                'id'           => $attribute->id,
                'key'          => str_random(15),
                'translations' => [],
                'image_url'    => $attribute->image->url(),
            ];

            foreach (languages() as $language) {
                $attributeData['translations'][$language->iso_code] = [
                    'name' => $attribute->translate($language->iso_code)->name ?? '',
                ];
            }

            $data['attributes'][] = $attributeData;
        }

        return collect($data);
    }

    /**
     * Format parameter for single client side product.
     *
     * @param \Modules\Product\Models\Product $product
     * @return array
     */
    public function forSingleClientSideProduct(Product $product): array
    {
        $data = [
            'id'         => $this->parameter->id,
            'name'       => $this->parameter->name,
            'type'       => $this->parameter->is_countable ? 'radio' : 'static',
            'countable'  => $this->parameter->is_countable,
            'attributes' => [],
        ];

        if ($this->parameter->type === 'range' || !$this->parameter->is_countable) {
            if ($this->parameter->type === 'range') {
                $data['value'] = optional($this->parameter->pivot)->value;
            } else {
                $productAttribute = $product->enabledAttributes->where('parameter_id', $this->parameter->id)->first();

                $data['value'] = optional($productAttribute)->name;

                if ($productAttribute && $productAttribute->image_file_name) {
                    $data['img'] = $productAttribute->image->url('thumb');
                }
            }

            return $data;
        }

        // Key by attribute id
        $attributes = $product->enabledAttributes->keyBy(function ($attribute) {
            return $attribute->pivot->parameter_attribute_id;
        });

        foreach ($this->parameter->attributes as $attribute) {
            if (!$productAttribute = $attributes->get($attribute->id)) {
                continue;
            }

            if ($this->parameter->display_as_range) {
                $name = $attribute->name . $this->parameter->postfix;
            } else {
                $name = $attribute->name;
            }

            $data['attributes'][] = [
                'id'       => $attribute->id,
                'name'     => $name,
                'quantity' => $productAttribute->pivot->quantity,
            ];
        }

        return $data;
    }
}