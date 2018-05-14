<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__product_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('currency_id');

            // Discount.
            $table->enum('discount_type', ['none', 'percent', 'money'])->default('none');
            $table->double('discount_amount')->default(0);

            // Prices with VAT.
            $table->decimal('with_vat_with_discount');
            $table->decimal('with_vat_without_discount');

            // Prices without VAT.
            $table->decimal('without_vat_with_discount');
            $table->decimal('without_vat_without_discount');

            $table->timestamps();

            $productForeign = 'product::product_prices-product_foreign';
            $currencyForeign = 'product::product_prices-currency_foreign';

            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
            $table->foreign('currency_id', $currencyForeign)->references('id')->on('netcore_country__currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__product_prices');
    }
}
