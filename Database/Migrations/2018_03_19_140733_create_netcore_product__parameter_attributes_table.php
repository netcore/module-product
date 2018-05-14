<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductParameterAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__parameter_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parameter_id');
            $table->string('image_file_name')->nullable();
            $table->integer('image_file_size')->nullable();
            $table->string('image_content_type')->nullable();
            $table->timestamp('image_updated_at')->nullable();
            $table->timestamps();

            $foreignKey = 'product::parameter_attributes-parameter_foreign';
            $table->foreign('parameter_id', $foreignKey)->references('id')->on('netcore_product__parameters')->onDelete('cascade');
        });

        Schema::create('netcore_product__parameter_attribute_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parameter_attribute_id');
            $table->string('locale', 2);
            $table->string('name');
            $table->timestamps();

            $uniqueKey = 'product::parameter_attribute_ts-attribute_locale_unique';
            $foreignKey = 'product::parameter_attribute_ts-attribute_foreign';
            $foreignKeyLocale = 'product::parameter_attribute_ts-locale_foreign';

            $table->unique(['parameter_attribute_id', 'locale'], $uniqueKey);
            $table->foreign('locale', $foreignKeyLocale)->references('iso_code')->on('languages')->onDelete('cascade');
            $table->foreign('parameter_attribute_id', $foreignKey)->references('id')->on('netcore_product__parameter_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__parameter_attribute_translations');
        Schema::dropIfExists('netcore_product__parameter_attributes');
    }
}
