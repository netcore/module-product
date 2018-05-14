<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductFieldOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__field_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_id');
            $table->timestamps();

            $foreignKey = 'product::field_options-field_foreign';
            $table->foreign('field_id', $foreignKey)->references('id')->on('netcore_product__fields')->onDelete('cascade');
        });

        Schema::create('netcore_product__field_option_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_option_id');
            $table->string('locale', 2)->index();
            $table->string('name');
            $table->timestamps();

            $uniqueKey = 'product::field_option_ts-field_option_locale_unique';
            $foreignKey = 'product::field_option_ts-field_option_foreign';
            $foreignKeyLocale = 'product::field_option_ts-locale_foreign';

            $table->unique(['field_option_id', 'locale'], $uniqueKey);
            $table->foreign('locale', $foreignKeyLocale)->references('iso_code')->on('languages')->onDelete('cascade');
            $table->foreign('field_option_id', $foreignKey)->references('id')->on('netcore_product__field_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__field_option_translations');
        Schema::dropIfExists('netcore_product__field_options');
    }
}
