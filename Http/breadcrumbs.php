<?php

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\Facades\DuplicateBreadcrumbException;
use Modules\Product\Models\Field;
use Modules\Product\Models\Parameter;
use Modules\Product\Models\Product;

try {
    Breadcrumbs::register('admin.products', function(BreadcrumbsGenerator $generator) {
        $generator->parent('admin');
        $generator->push('Products', route('product::products.index'));
    });

    // Product fields
    Breadcrumbs::register('admin.products.fields', function(BreadcrumbsGenerator $generator) {
        $generator->parent('admin.products');
        $generator->push('Product fields', route('product::fields.index'));
    });

    Breadcrumbs::register('admin.products.fields.create', function(BreadcrumbsGenerator $generator) {
        $generator->parent('admin.products.fields');
        $generator->push('Create product field', route('product::fields.create'));
    });

    Breadcrumbs::register('admin.products.fields.edit', function(BreadcrumbsGenerator $generator, Field $field) {
        $generator->parent('admin.products.fields');
        $generator->push('Edit product field - ' . $field->name, route('product::fields.edit', $field));
    });

    // Products
    Breadcrumbs::register('admin.products.create', function(BreadcrumbsGenerator $generator) {
        $generator->parent('admin.products');
        $generator->push('Create product', route('product::products.create'));
    });

    Breadcrumbs::register('admin.products.edit', function(BreadcrumbsGenerator $generator, Product $product) {
        $generator->parent('admin.products');
        $generator->push('Edit product - #' . $product->id, route('product::products.edit', $product));
    });

    // Product parameters
    Breadcrumbs::register('admin.products.parameters', function(BreadcrumbsGenerator $generator) {
        $generator->parent('admin.products');
        $generator->push('Product parameters', route('product::parameters.index'));
    });

    Breadcrumbs::register('admin.products.parameters.create', function(BreadcrumbsGenerator $generator) {
        $generator->parent('admin.products.parameters');
        $generator->push('Create parameter', route('product::parameters.create'));
    });

    Breadcrumbs::register('admin.products.parameters.edit', function(BreadcrumbsGenerator $generator, Parameter $parameter) {
        $generator->parent('admin.products.parameters');
        $generator->push('Edit parameter', route('product::parameters.edit', $parameter));
    });

} catch (DuplicateBreadcrumbException $e) {
    abort(500, $e->getMessage());
}