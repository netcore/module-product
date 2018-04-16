<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Product\Models\Field;

class FieldsRepository
{
    /**
     * Get global fields + category related fields.
     *
     * @param array|Collection $categories
     * @return \Illuminate\Support\Collection
     */
    public function getFieldsOfCategories($categories): Collection
    {
        if ($categories instanceof Collection) {
            $categories = $categories->pluck('id')->toArray();
        }

        $fields = Field::with('options')->whereIsGlobal(true)->get();

        foreach ($categories as $categoryId) {
            $descendantAndSelfIds = Category::descendantsAndSelf($categoryId);

            $categoryFields = Field::with('options')->whereIsGlobal(false);
            $categoryFields = $categoryFields->whereHas('categories', function ($query) use ($descendantAndSelfIds) {
                return $query->whereIn('id', $descendantAndSelfIds);
            })->get();

            $fields = $fields->merge($categoryFields);
        }

        return collect($fields)->unique('id');
    }
}