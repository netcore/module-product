<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductCategoryProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__category_product', function (Blueprint $table) {
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('product_id');

            $uniqueKey = 'product::category_product-category_product_unique';
            $productForeign = 'product::category_product-product_foreign';
            $categoryForeign = 'product::category_product-category_foreign';

            $table->unique(['category_id', 'product_id'], $uniqueKey);
            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('category_id', $categoryForeign)->references('id')->on('netcore_category__categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__category_product');
    }
}
