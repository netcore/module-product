<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__fields', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_global')->default(false);
            $table->boolean('is_translatable')->default(false);
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('netcore_product__field_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('field_id');
            $table->string('locale', 2);
            $table->string('name');
            $table->timestamps();

            $table->unique(['field_id', 'locale'], 'netcore_product__ft_f_l_unique');

            $table
                ->foreign('field_id', 'netcore_product_ft_f_foreign')
                ->references('id')
                ->on('netcore_product__fields')
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
        Schema::dropIfExists('netcore_product__field_translations');
        Schema::dropIfExists('netcore_product__fields');
    }
}
