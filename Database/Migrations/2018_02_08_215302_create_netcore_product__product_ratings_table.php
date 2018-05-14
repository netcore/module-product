<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreProductProductRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_product__product_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->ipAddress('ip_address');
            $table->unsignedInteger('rate');
            $table->timestamps();

            $usersTable = config('netcore.module-admin.user.table', 'users');
            $productForeign = 'product::product_ratings-product_foreign';
            $userForeign = 'product::product_ratings-user_foreign';

            $table->foreign('user_id', $userForeign)->references('id')->on($usersTable)->onDelete('set null');
            $table->foreign('product_id', $productForeign)->references('id')->on('netcore_product__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_product__product_ratings');
    }
}
