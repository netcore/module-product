<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $languages = languages();
        $rules = [];

        foreach ($languages as $language) {
            $rules["translations.{$language->iso_code}.title"] = 'required';
            $rules["translations.{$language->iso_code}.slug"] = 'nullable|unique:netcore_product__product_translations';
        }

        // @TODO: other rules.
        
        return $rules;
    }
}