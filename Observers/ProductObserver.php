<?php

namespace Modules\Product\Observers;

use Artisan;
use Modules\Product\Models\Product;

class ProductObserver
{
    /**
     * Product created event listener.
     *
     * @param \Modules\Product\Models\Product $product
     * @return void
     */
    public function created(Product $product): void
    {
        Artisan::queue('product:update-category-products-count');
    }

    /**
     * Product deleted event listener.
     *
     * @param \Modules\Product\Models\Product $product
     * @return void
     */
    public function deleted(Product $product): void
    {
        Artisan::queue('product:update-category-products-count');
    }
}