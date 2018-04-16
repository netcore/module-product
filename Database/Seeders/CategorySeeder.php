<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\CategoryGroup;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run(): void
    {
        $config = config('netcore.module-product.used_category_group', [
            'key'  => 'product',
            'name' => 'Product categories',
        ]);

        CategoryGroup::updateOrCreate(
            array_only($config, 'key'), $config
        );
    }
}