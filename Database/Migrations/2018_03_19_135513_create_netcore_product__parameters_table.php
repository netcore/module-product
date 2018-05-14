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
            $table->boolean('is_countable')->default(false);
            $table->boolean('is_price')->default(false);
            $table->boolean('display_as_range')->default(false);
            $table->boolean('display_as_detail')->default(false);
            $table->boolean('iconable_attributes')->default(false);
            $table->text('postfix')->nullable();

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

            $uniqueKey = 'product::parameter_ts-locale_parameter_unique';
            $foreignKey = 'product::parameter_ts-parameter_foreign';
            $foreignKeyLocale = 'product::parameter_ts-locale_foreign';

            $table->unique(['parameter_id', 'locale'], $uniqueKey);
            $table->foreign('locale', $foreignKeyLocale)->references('iso_code')->on('languages')->onDelete('cascade');
            $table->foreign('parameter_id', $foreignKey)->references('id')->on('netcore_product__parameters')->onDelete('cascade');
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
