<?php

namespace Modules\Product\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCategoryProductsCount extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'product:update-category-products-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recount and update products count for each category.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $categories = product()->getCategoryGroup()->categories;

        foreach ($categories as $category) {
            $descendantsAndSelf = Category::descendantsAndSelf($category->id)->pluck('id')->toArray();

            $category->items_count = Product::whereHas('categories', function (Builder $builder) use ($descendantsAndSelf) {
                return $builder->whereIn('id', $descendantsAndSelf);
            })->count();

            $category->save();

            $this->info('[' . $category->name . '] has ' . $category->items_count . ' product/-s.');
        }
    }
}
