<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/products',
    'as'         => 'product::',
    'namespace'  => 'Modules\Product\Http\Controllers',
], function () {
    Route::view('/', 'product::index');

    // API routes.
    Route::group(['prefix' => 'api'], function () {
        // Base.
        Route::get('init', 'BaseController@init');
        Route::get('category-data', 'BaseController@getCategoryData');

        // Parameters.
        Route::get('parameters', 'ParameterController@paginate');
        Route::post('parameters', 'ParameterController@store');
        Route::get('parameters/{parameter}', 'ParameterController@show');
        Route::put('parameters/{parameter}', 'ParameterController@update');
        Route::delete('parameters/{parameter}', 'ParameterController@destroy');

        // Fields.
        Route::get('fields', 'FieldController@paginate');
        Route::post('fields', 'FieldController@store');
        Route::get('fields/{field}', 'FieldController@show');
        Route::put('fields/{field}', 'FieldController@update');
        Route::delete('fields/{field}', 'FieldController@destroy');

        // Products.
        Route::get('products', 'ProductController@paginate');
        Route::post('products', 'ProductController@store');
        Route::get('products/{product}', 'ProductController@show');
        Route::put('products/{product}', 'ProductController@update');
        Route::delete('products/{product}', 'ProductController@destroy');

        // Shipping options.
        Route::get('shipping-options', 'ShippingOptionController@index');
        Route::post('shipping-options', 'ShippingOptionController@store');
        Route::put('shipping-options/{shippingOption}', 'ShippingOptionController@update');
        Route::get('shipping-options/{shippingOption}', 'ShippingOptionController@show');
        Route::delete('shipping-options/{shippingOption}', 'ShippingOptionController@destroy');
        Route::post('shipping-options/toggle-state/{shippingOption}', 'ShippingOptionController@toggleState');
    });
});

Route::group([
    'middleware' => 'web',
    'prefix'     => 'api/product',
    'as'         => 'product::api.',
    'namespace'  => 'Modules\Product\Http\Controllers\ClientAPI',
], function () {
    // Cart.
    Route::get('cart/items', 'CartController@getItems')->name('cart.items');
    Route::post('cart/insert/{product}', 'CartController@insertItem')->name('cart.item.insert');
    Route::post('cart/remove/{cartItem}', 'CartController@removeItem')->name('cart.item.remove');
    Route::post('cart/update-quantity/{cartItem}', 'CartController@updateQuantity')->name('cart.item.update-quantity');

    // Wishlist.
    Route::get('wishlist/items', 'WishlistController@getItems')->name('wishlist.items');
    Route::post('wishlist/insert/{product}', 'WishlistController@insertItem')->name('wishlist.item.insert');
    Route::post('wishlist/remove/{wishlistItem}', 'WishlistController@removeItem')->name('wishlist.item.remove');
    Route::post('wishlist/move-to-cart/{wishlistItem}', 'WishlistController@moveItemToCart')->name('wishlist.item.move-to-cart');

    // Misc.
    Route::get('shipping-options', 'ShippingOptionController@index')->name('shipping-options.index');
    Route::post('rate/{product}', 'RatingController@submitRate')->name('rating.submit');
});