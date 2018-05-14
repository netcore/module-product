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
            $table->text('description')->nullable();

            $table->timestamps();

            $uniqueKey = 'product::product_ts-product_locale_unique';
            $foreignKey = 'product::product_ts-product_foreign';
            $foreignKeyLocale = 'product::product_ts-locale_foreign';

            $table->unique(['product_id', 'locale'], $uniqueKey);
            $table->foreign('locale', $foreignKeyLocale)->references('iso_code')->on('languages')->onDelete('cascade');
            $table->foreign('product_id', $foreignKey)->references('id')->on('netcore_product__products')->onDelete('cascade');
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
