<?php

Route::group([
    'middleware' => ['web', 'auth.admin'],
    'prefix'     => 'admin/products',
    'as'         => 'product::',
    'namespace'  => 'Modules\Product\Http\Controllers',
], function () {

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

    Route::resource('parameters', 'ParameterController', [
        'except' => ['show'],
    ]);

    Route::get('parameters/paginate', [
        'uses' => 'ParameterController@paginate',
        'as'   => 'parameters.paginate',
    ]);

    /**
     * Settings.
     */
    Route::post('settings/save', [
        'uses' => 'ProductSettingController@update',
        'as'   => 'settings.update',
    ]);

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