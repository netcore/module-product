<?php

namespace Modules\Product\ViewComposers;

use Illuminate\View\View;
use Modules\Category\Models\CategoryGroup;
use Modules\Product\Models\Field;

class FieldFormComposer
{
    /**
     * Pass data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose(View $view): void
    {
        $types = collect(Field::TYPES);
        $languages = languages();

        $categoryGroupKey = config('netcore.module-product.used_category_group.key');
        $categoryGroup = CategoryGroup::where(['key' => $categoryGroupKey])->firstOrFail();

        $categories = $categoryGroup->categories->map(function ($category) {
            return [
                'id'   => $category->id,
                'text' => $category->chainedName
            ];
        });

        $view->with(compact('categories', 'types', 'languages'));
    }
}