<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductCategoryFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__category_field', function (Blueprint $table) {
            $table->unsignedInteger('field_id');
            $table->unsignedInteger('category_id');

            $table->unique(['field_id', 'category_id']);

            $table->foreign('field_id')->references('id')->on('netcore_product__fields')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('netcore_category__categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__category_field');
    }
}
