<?php

namespace Modules\Product\Traits\Models;

trait TranslationHelpers
{
    /**
     * Sync translations.
     *
     * @param array $translations
     */
    public function syncTranslations(array $translations): void
    {
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