<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductProductFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__product_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('field_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('netcore_product__fields')->onDelete('cascade');
        });

        Schema::create('netcore_product__product_field_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_field_id');
            $table->string('locale', 2)->index();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['product_field_id', 'locale'], 'nc_product__product_field_translations_unique');

            $table
                ->foreign('product_field_id', 'nc_product__product_field_translations_product_field_id_foreign')
                ->references('id')
                ->on('netcore_product__product_fields')
                ->onDelete('cascade');

            $table
                ->foreign('locale', 'nc_product__product_field_translations_locale_foreign')
                ->references('iso_code')
                ->on('languages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__product_field_translations');
        Schema::dropIfExists('netcore_product__product_fields');
    }
}
