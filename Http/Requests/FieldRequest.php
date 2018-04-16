<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Netcore\Translator\Helpers\TransHelper;

class FieldRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'is_global' => 'boolean',
        ];

        foreach (TransHelper::getAllLanguages() as $language) {
            $rules['translations.' . $language->iso_code . '.name'] = 'required';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach (TransHelper::getAllLanguages() as $language) {
            $message = 'Name for ' . $language->title_localized . ' language is required!';
            $rules['translations.' . $language->iso_code . '.name.required'] = $message;
        }

        return $attributes;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
