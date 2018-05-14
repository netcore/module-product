<?php

namespace Modules\Product\PassThroughs\Field;

use Modules\Product\Models\Field;
use Modules\Product\PassThroughs\PassThrough;

class Format extends PassThrough
{
    /**
     * Field model instance.
     *
     * @var \Modules\Product\Models\Field
     */
    protected $field;

    /**
     * Format constructor.
     *
     * @param \Modules\Product\Models\Field $field
     */
    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * Format data for single admin side field.
     *
     * @return array
     */
    public function forSingleAdminSideField(): array
    {
        $data = [
            'type'            => $this->field->type,
            'is_global'       => $this->field->is_global,
            'is_translatable' => $this->field->is_translatable,
            'categories'      => $this->field->categories->pluck('id'),
            'translations'    => [],
            'options'         => [],
        ];

        foreach (languages() as $language) {
            $data['translations'][$language->iso_code] = [
                'name' => $this->field->translations->where('locale', $language->iso_code)->first()->name ?? '',
            ];
        }

        foreach ($this->field->options as $i => $option) {
            $data['options'][$i] = [
                'id'           => $option->id,
                'translations' => [],
            ];

            foreach (languages() as $language) {
                $data['options'][$i]['translations'][$language->iso_code] = [
                    'name' => $option->translations->where('locale', $language->iso_code)->first()->name ?? '',
                ];
            }
        }

        return $data;
    }
}