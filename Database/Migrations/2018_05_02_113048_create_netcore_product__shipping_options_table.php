<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductShippingOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__shipping_options', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('price');
            $table->decimal('price_when_free')->nullable();
            $table->string('type')->unique();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_free_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('netcore_product__shipping_option_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shipping_option_id');
            $table->string('locale', 2);
            $table->string('name');
            $table->timestamps();

            $uniqueKey = 'product::shipping_option_ts-locale_option_unique';
            $localeForeign = 'product::shipping_option_ts-locale_foreign';
            $shippingOptionForeign = 'product::shipping_option_ts-shipping_option_foreign';

            $table->unique(['locale', 'shipping_option_id'], $uniqueKey);
            $table->foreign('shipping_option_id', $shippingOptionForeign)->references('id')->on('netcore_product__shipping_options')->onDelete('cascade');
            $table->foreign('locale', $localeForeign)->references('iso_code')->on('languages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__shipping_option_translations');
        Schema::dropIfExists('netcore_product__shipping_options');
    }
}
