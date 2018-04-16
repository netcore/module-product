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
            $table->timestamps();

            $table->foreign('wishlist_id')->references('id')->on('netcore_product__wishlists')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('netcore_product__products')->onDelete('cascade');
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
