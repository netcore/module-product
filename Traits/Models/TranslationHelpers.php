<?php

namespace Modules\Product\Traits\Models;

use Illuminate\Support\Collection;

trait TranslationHelpers
{
    /**
     * Sync translations.
     *
     * @param array|\Illuminate\Support\Collection $translations
     */
    public function syncTranslations($translations): void
    {
        if (!is_array($translations) && !$translations instanceof Collection) {
            abort(500, 'Incorrect data passed to syncTranslations!');
        }

        foreach ($translations as $locale => $translationData) {
            $existing = $this->translations->where('locale', $locale)->first();

            // Filter translatable fields.
            $translationData = array_only($translationData, $this->translatedAttributes ?? []);

            if ($existing) {
                $existing->update($translationData);
            } else {
                $this->translations()->create(
                    ['locale' => $locale] + $translationData
                );
            }
        }
    }
}