<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParameterRequest extends FormRequest
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
        $isEdit = !!$this->route('parameter');

        $rules = [
            'type' => 'required|in:radio,checkbox,range',
        ];

        foreach (languages() as $language) {
            $rules["translations.{$language->iso_code}.name"] = 'required';
        }

        if ($this->input('type') === 'range') {
            $rules += [
                'value_from' => 'required|integer',
                'value_to'   => 'required|integer',
            ];
        } else {
            foreach ($this->input('attributes', []) as $index => $attribute) {
                if ($this->input('iconable_attributes') && !$isEdit) {
                    $rules["attributes.{$index}.image"] = 'required|image';
                }

                foreach (languages() as $language) {
                    $rules["attributes.{$index}.translations.{$language->iso_code}"] = 'required';
                }
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];

        foreach (languages() as $language) {
            $messages["translations.{$language->iso_code}.name.required"] = "Please enter parameter name for {$language->title} language!";
        }

        dd($messages);
    }
}
