<?php

namespace Modules\Product\PassThroughs\ProductField;

use Modules\Product\Models\ProductField;
use Modules\Product\PassThroughs\PassThrough;

class Format extends PassThrough
{
    /**
     * Product field model.
     *
     * @var \Modules\Product\Models\ProductField
     */
    protected $productField;

    /**
     * Format constructor.
     *
     * @param \Modules\Product\Models\ProductField $productField
     */
    public function __construct(ProductField $productField)
    {
        $this->productField = $productField;
    }

    /**
     * Format product field for single product.
     *
     * @return array
     */
    public function forSingleClientSideProduct(): array
    {
        if ($this->productField->field->type === 'checkbox') {
            $value = $this->productField->value ? lg('app.yes_value', 'Yes') : lg('app.no_value', 'No');
        } else {
            $value = $this->productField->value;
        }

        return [
            'name'  => $this->productField->field->name ?? '',
            'value' => $value ?? 'N/A',
        ];
    }
}