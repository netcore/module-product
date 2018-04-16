<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__products', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_variable')->default(false); // Product can be variable.
            $table->unsignedInteger('parent_id')->nullable(); // In case of variable product.

            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('average_rating')->default(0);

            $table->timestamps();
        });

        Schema::create('netcore_product__product_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->string('locale', 2);

            $table->string('title');
            $table->string('slug')->unique()->index();

            $table->timestamps();

            $table->unique(['product_id', 'locale']);
            $table->foreign('product_id')->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('locale')->references('iso_code')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__product_translations');
        Schema::dropIfExists('netcore_product__products');
    }
}
