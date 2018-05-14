<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductShippingOptionLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__shipping_option_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shipping_option_id');
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('zip');
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->enum('imported_from', ['dpd', 'omniva', 'none'])->default('none');
            $table->timestamps();

            $foreignKey = 'product::shipping_option_locations-shipping_option_foreign';
            $table->foreign('shipping_option_id', $foreignKey)->references('id')->on('netcore_product__shipping_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__shipping_option_locations');
    }
}
