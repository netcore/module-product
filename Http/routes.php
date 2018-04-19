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
        Route::get('languages', 'BaseController@languages');
        Route::get('currencies', 'BaseController@currencies');
        Route::get('categories', 'BaseController@categories');

        // Parameters.
        Route::get('parameters', 'ParameterController@paginate');
        Route::post('parameters', 'ParameterController@store');
        Route::get('parameters/{parameter}', 'ParameterController@show');
        Route::put('parameters/{parameter}', 'ParameterController@update');
        Route::delete('parameters/{parameter}', 'ParameterController@destroy');

        // Fields.
        Route::get('fields/get-types', 'BaseController@productFieldTypes');

        Route::get('fields', 'FieldController@paginate');
        Route::post('fields', 'FieldController@store');
        Route::get('fields/{field}', 'FieldController@show');
        Route::put('fields/{field}', 'FieldController@update');
        Route::delete('fields/{field}', 'FieldController@destroy');

        // Products.
        Route::get('products/fields', 'BaseController@productFields');

        Route::get('products', 'ProductController@paginate');
        Route::post('products', 'ProductController@store');
        Route::get('products/{product}', 'ProductController@show');
        Route::put('products/{product}', 'ProductController@update');
        Route::delete('products/{product}', 'ProductController@destroy');
    });
});