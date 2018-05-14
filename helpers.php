<?php

use Modules\Country\Models\Currency;
use Modules\Product\Repositories\CartRepository;
use Modules\Product\Repositories\ProductsRepository;
use Modules\Product\Repositories\WishlistRepository;

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

if (!function_exists('cart')) {
    function cart(): CartRepository
    {
        return new CartRepository;
    }
}

if (!function_exists('wishlist')) {
    function wishlist(): WishlistRepository
    {
        try {
            return new WishlistRepository;
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}

if (!function_exists('price')) {
    function price($price)
    {
        return number_format($price, 2, '.', '');
    }
}