<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductParameterProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__parameter_product', function (Blueprint $table) {
            $table->unsignedInteger('parameter_id');
            $table->unsignedInteger('product_id');

            $table->string('value')->nullable(); // used for range type parameter values.

            $uniqueKey = 'product::parameter_product-parameter_product_unique';
            $productForeign = 'product::parameter_product-product_foreign';
            $parameterForeign = 'product::parameter_product-parameter-foreign';

            $table->unique(['parameter_id', 'product_id'], $uniqueKey);
            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('parameter_id', $parameterForeign)->references('id')->on('netcore_product__parameters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__parameter_product');
    }
}
