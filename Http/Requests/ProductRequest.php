<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Product\Rules\Slug;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $languages = languages();

        $rules = [
            'categories'   => 'required|array|min:1',
            'categories.*' => 'exists:netcore_category__categories,id',
        ];

        // Translations.
        if (!$this->input('is_variable')) {
            foreach ($languages as $language) {
                $rules["translations.{$language->iso_code}.title"] = 'required';

                $slugRule = ['nullable', new Slug, Rule::unique('netcore_product__product_translations', 'slug')];

                if ($product && $translationItem = $product->translations->where('locale', $language->iso_code)->first()) {
                    $slugRule[2]->ignore($translationItem->id);
                }

                $rules["translations.{$language->iso_code}.slug"] = $slugRule;
            }
        } else {
            foreach ($this->input('variants', []) as $index => $variant) {
                foreach ($languages as $language) {
                    $rules["variants.{$index}.translations.{$language->iso_code}.title"] = 'required';

                    $slugRule = ['nullable', new Slug, Rule::unique('netcore_product__product_translations', 'slug')];

                    if ($product && $variantId = $this->input("variants.{$index}.id")) {
                        $variantInstance = $product->children()->find($variantId);

                        if ($variantInstance && $translationItem = $variantInstance->translations->where('locale', $language->iso_code)->first()) {
                            $slugRule[2]->ignore($translationItem->id);
                        }
                    }

                    $rules["translations.{$language->iso_code}.slug"] = $slugRule;
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
        return [
            'categories.required' => 'Please select at least one category.',
            'categories.array'    => 'Wrong data passed, possible JS logic error.',
            'categories.min'      => 'Please select at least one category.',
            'categories.*.exists' => 'Non-existent category selected.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $attributes = [];

        foreach (languages() as $language) {
            $attributes["translations.{$language->iso_code}.title"] = 'title';
            $attributes["translations.{$language->iso_code}.slug"] = 'slug';
        }

        foreach ($this->input('variants', []) as $index => $variantData) {
            foreach (languages() as $language) {
                $attributes["variants.{$index}.translations.{$language->iso_code}.title"] = 'title';
                $attributes["variants.{$index}.translations.{$language->iso_code}.slug"] = 'slug';
            }
        }

        return $attributes;
    }
}