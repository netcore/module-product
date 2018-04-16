<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/products',
    'as'         => 'product::',
    'namespace'  => 'Modules\Product\Http\Controllers',
], function () {

    Route::view('/', 'product::index');


    // @TODO - refactor.


    Route::resource('products', 'ProductController');

    Route::delete('products/{product}/images/{image}', [
        'uses' => 'ProductImageController@destroy',
        'as'   => 'products.images.destroy',
    ]);

    Route::post('products/{product}/images/reorder', [
        'uses' => 'ProductImageController@saveImagesOrder',
        'as'   => 'products.images.reorder',
    ]);

    Route::post('products/{product}/images/{image}/mark-as-preview', [
        'uses' => 'ProductImageController@markImageAsPreview',
        'as'   => 'products.images.mark-as-preview',
    ]);

    Route::resource('fields', 'FieldController', [
        'except' => ['show'],
    ]);

    Route::get('fields/paginate', [
        'uses' => 'FieldController@paginate',
        'as'   => 'fields.paginate',
    ]);

    /**
     * Settings.
     */
    Route::post('settings/save', [
        'uses' => 'ProductSettingController@update',
        'as'   => 'settings.update',
    ]);

    // API routes.
    Route::group(['prefix' => 'api'], function () {
        Route::get('languages', 'BaseController@languages');

        // Parameters.
        Route::get('parameters', 'ParameterController@paginate');
        Route::post('parameters', 'ParameterController@store');
        Route::get('parameters/{parameter}', 'ParameterController@show');
        Route::put('parameters/{parameter}', 'ParameterController@update');
        Route::delete('parameters/{parameter}', 'ParameterController@destroy');
    });

    /**
     * API.
     */
    Route::get('api/init-app', [
        'uses' => 'ApiController@getInitialData',
        'as'   => 'api.get-initialData',
    ]);

    Route::get('api/get-product-data/{product?}', [
        'uses' => 'ApiController@getProductData',
        'as'   => 'api.get-product-data',
    ]);

    Route::get('api/get-product-fields/{product?}', [
        'uses' => 'ApiController@getProductFields',
        'as'   => 'api.get-product-fields',
    ]);

});