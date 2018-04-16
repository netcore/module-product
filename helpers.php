<?php

use Modules\Country\Models\Currency;
use Modules\Product\Repositories\ProductsRepository;

if (!function_exists('getSelectListOfCurrencies')) {
    function getSelectListOfCurrencies(): array
    {
        return Currency::all()->mapWithKeys(function (Currency $currency) {
            return [$currency->id => "{$currency->code} - {$currency->symbol}"];
        })->toArray();
    }
}

if (!function_exists('product')) {
    function product(): ProductsRepository
    {
        return new ProductsRepository;
    }
}