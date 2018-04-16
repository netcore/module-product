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

            $table
                ->foreign('field_id', 'netcore_product__fo_f_foreign')
                ->references('id')
                ->on('netcore_product__fields')
                ->onDelete('cascade');
        });

        Schema::create('netcore_product__field_option_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_option_id');
            $table->string('locale', 2)->index();
            $table->string('name');
            $table->timestamps();

            $table->unique(['field_option_id', 'locale'], 'netcore_product__fot_f_l_unique');

            $table
                ->foreign('locale', 'netcore_product__fot_l_foreign')
                ->references('iso_code')
                ->on('languages')
                ->onDelete('cascade');

            $table
                ->foreign('field_option_id', 'netcore_product__fot_fo_foreign')
                ->references('id')
                ->on('netcore_product__field_options')
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
        Schema::dropIfExists('netcore_product__field_option_translations');
        Schema::dropIfExists('netcore_product__field_options');
    }
}
