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

            $fieldForeign = 'product::product_fields-field_foreign';
            $productForeign = 'product::product_fields-product_foreign';

            $table->foreign('field_id', $fieldForeign)->references('id')->on('netcore_product__fields')->onDelete('cascade');
            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
        });

        Schema::create('netcore_product__product_field_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_field_id');
            $table->string('locale', 2)->index();
            $table->text('value')->nullable();
            $table->timestamps();

            $uniqueKey = 'product::product_field_ts-product_field_locale_unique';
            $foreignKey = 'product::product_field_ts-product_field_foreign';
            $foreignKeyLocale = 'product::product_field_ts-locale_foreign';

            $table->unique(['product_field_id', 'locale'], $uniqueKey);
            $table->foreign('locale', $foreignKeyLocale)->references('iso_code')->on('languages')->onDelete('cascade');
            $table->foreign('product_field_id', $foreignKey)->references('id')->on('netcore_product__product_fields')->onDelete('cascade');
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
