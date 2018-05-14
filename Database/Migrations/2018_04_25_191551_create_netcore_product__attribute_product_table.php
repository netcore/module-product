<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductAttributeProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__attribute_product', function (Blueprint $table) {
            $table->unsignedInteger('parameter_attribute_id');
            $table->unsignedInteger('product_id');

            $table->unsignedInteger('quantity')->nullable(); // for countable attributes.

            $uniqueKey = 'product::attribute_product-parameter_product_unique';
            $productForeign = 'product::attribute_product-product_foreign';
            $attributeForeign = 'product::attribute_product-parameter_attribute_foreign';

            $table->unique(['parameter_attribute_id', 'product_id'], $uniqueKey);
            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('parameter_attribute_id', $attributeForeign)->references('id')->on('netcore_product__parameter_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__attribute_product');
    }
}
