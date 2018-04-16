<?php

namespace Modules\Product\ViewComposers;

use Illuminate\View\View;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryGroup;
use Modules\Country\Models\Country;
use Modules\Product\Models\ProductSetting;

class ProductFormComposer
{
    /**
     * Default fallback VAT.
     *
     * @var int
     */
    protected $defaultVat = 21;

    /**
     * Pass data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose(View $view): void
    {
        // Categories.
        $categoryGroup = CategoryGroup::where([
            'key' => config('netcore.module-product.used_category_group.key'),
        ])->firstOrFail();

        $categories = $categoryGroup->categories->map(function ($category) {
            return [
                'id'   => $category->id,
                'text' => $category->chainedName,
            ];
        });

        // Languages for translations and vat.
        $languages = languages();
        $currencies = product()->getCurrencies();

        $view->with(compact('categories', 'languages', 'currencies'));
    }
}