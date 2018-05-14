<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cart_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('attribute_id')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();

            $cartForeign = 'product::cart_items-cart_foreign';
            $productForeign = 'product::cart_items-product_foreign';
            $attributeForeign = 'product::cart_items-attribute_foreign';

            $table->foreign('cart_id', $cartForeign)->references('id')->on('netcore_product__carts')->onDelete('cascade');
            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('attribute_id', $attributeForeign)->references('id')->on('netcore_product__parameter_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__cart_items');
    }
}
