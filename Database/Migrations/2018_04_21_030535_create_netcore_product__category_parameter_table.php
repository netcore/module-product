<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductCategoryParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__category_parameter', function (Blueprint $table) {
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('parameter_id');

            $uniqueKey = 'product::category_parameter-category_parameter_unique';
            $categoryForeign = 'product::category_parameter-category_foreign';
            $parameterForeign = 'product::category_parameter-parameter-foreign';

            $table->unique(['category_id', 'parameter_id'], $uniqueKey);
            $table->foreign('category_id', $categoryForeign)->references('id')->on('netcore_category__categories')->onDelete('cascade');
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
        Schema::dropIfExists('netcore_product__category_parameter');
    }
}
