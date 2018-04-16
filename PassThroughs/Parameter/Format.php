<?php

namespace Modules\Product\PassThroughs\Parameter;

use Modules\Product\Models\Parameter;
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
            'type'         => $this->parameter->type,
            'value_from'   => $this->parameter->value_from,
            'value_to'     => $this->parameter->value_to,
            'attributes'   => [],
            'translations' => [],

            'is_static'           => (bool)$this->parameter->is_static,
            'iconable_attributes' => (bool)$this->parameter->iconable_attributes,
        ];

        // Map parameter translations.
        foreach (languages() as $language) {
            $data['translations'][$language->iso_code] = [
                'name' => optional($this->parameter->translate($language->iso_code))->name,
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
                    'name' => optional($attribute->translate($language->iso_code))->name,
                ];
            }

            $data['attributes'][] = $attributeData;
        }

        return collect($data);
    }
}