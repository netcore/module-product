<?php

namespace Modules\Product\Traits\Models;

use Modules\Product\Models\Field;

trait FieldTranslatable
{
    protected static $fields = [];

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        list($attribute, $locale) = $this->getAttributeAndLocale($key);

        if ($key !== 'value') {
            return parent::getAttribute($key);
        }

        if ($this->isTranslationAttribute($attribute) && $this->isCurrentFieldTranslatable()) {
            if ($this->getTranslation($locale) === null) {
                return $this->getAttributeValue($attribute);
            }

            // If the given $attribute has a mutator, we push it to $attributes and then call getAttributeValue
            // on it. This way, we can use Eloquent's checking for Mutation, type casting, and
            // Date fields.
            if ($this->hasGetMutator($attribute)) {
                $this->attributes[$attribute] = $this->getAttributeOrFallback($locale, $attribute);

                return $this->getAttributeValue($attribute);
            }

            return $this->getAttributeOrFallback($locale, $attribute);
        }

        return parent::getAttribute($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        list($attribute, $locale) = $this->getAttributeAndLocale($key);

        if ($key !== 'value') {
            return parent::setAttribute($key, $value);
        }

        if ($this->isTranslationAttribute($attribute) && $this->isCurrentFieldTranslatable()) {
            $this->getTranslationOrNew($locale)->$attribute = $value;
        } else {
            return parent::setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Determine if associated field is translatable.
     *
     * @return bool
     */
    private function isCurrentFieldTranslatable(): bool
    {
        return $this->field && $this->field->is_translatable;
    }
}