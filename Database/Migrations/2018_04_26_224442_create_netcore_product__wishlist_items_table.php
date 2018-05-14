<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductWishlistItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__wishlist_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('wishlist_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('attribute_id')->nullable();
            $table->timestamps();

            $productForeign = 'product::wishlist_items-product_foreign';
            $wishlistForeign = 'product::wishlist_items-wishlist_foreign';
            $attributeForeign = 'product::wishlist_items-attribute_foreign';

            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('wishlist_id', $wishlistForeign)->references('id')->on('netcore_product__wishlists')->onDelete('cascade');
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
        Schema::dropIfExists('netcore_product__wishlist_items');
    }
}