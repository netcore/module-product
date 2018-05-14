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
                foreach (languages() as $language) {
                    $rules["attributes.{$index}.translations.{$language->iso_code}.name"] = 'required';
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        $messages = [];

        foreach (languages() as $language) {
            $messages["translations.{$language->iso_code}.name.required"] = "Please provide parameter name for {$language->title} language.";
        }

        foreach ($this->input('attributes', []) as $index => $attribute) {
            foreach (languages() as $language) {
                $message = "Please provide translation for {$language->title} language of {$this->ordinal($index + 1)} parameter.";
                $messages["attributes.{$index}.translations.{$language->iso_code}.name.required"] = $message;
            }
        }

        return $messages;
    }

    /**
     * Ordinal suffix.
     *
     * @param $number
     * @return string
     */
    private function ordinal($number): string
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }

        return $number . $ends[$number % 10];
    }
}
