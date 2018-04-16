<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductParametersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__parameters', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['radio', 'checkbox', 'range']);
            $table->boolean('is_static')->default(false);
            $table->boolean('iconable_attributes')->default(false);

            // In case of range parameter.
            $table->string('value_from')->nullable();
            $table->string('value_to')->nullable();

            $table->timestamps();
        });

        Schema::create('netcore_product__parameter_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parameter_id');
            $table->string('locale', 2);
            $table->string('name');
            $table->timestamps();

            $table->unique(['parameter_id', 'locale'], 'nc_product__product_parameter_translations_unique');
            $table->foreign('parameter_id')->references('id')->on('netcore_product__parameters')->onDelete('cascade');
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
        Schema::dropIfExists('netcore_product__parameter_translations');
        Schema::dropIfExists('netcore_product__parameters');
    }
}
