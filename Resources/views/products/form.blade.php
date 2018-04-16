@extends('admin::layouts.master')

@section('content')

    @if(isset($product))
        {{ Breadcrumbs::render('admin.products.edit', $product) }}
    @else
        {{ Breadcrumbs::render('admin.products.create') }}
    @endif

    <div class="page-header">
        <h1>
            <span class="text-muted font-weight-light">
                <i class="page-header-icon fa fa-shopping-cart"></i>
                Products
            </span>
        </h1>
    </div>

    <div id="product-app">
        @if(isset($product))
            <product-form
                    method="PUT"
                    :languages="{{ $languages }}"
                    :currencies="{{ $currencies }}"
                    :categories="{{ $categories }}"
                    route="{{ route('product::products.update', $product) }}"
                    fields-route="{{ route('product::api.get-product-fields') }}"
                    product-route="{{ route('product::products.show', $product) }}"
                    image-reorder-route="{{ route('product::products.images.reorder', '--ID--') }}">
            </product-form>
        @else
            <product-form
                    method="POST"
                    :languages="{{ $languages }}"
                    :currencies="{{ $currencies }}"
                    :categories="{{ $categories }}"
                    route="{{ route('product::products.store') }}"
                    fields-route="{{ route('product::api.get-product-fields') }}">
            </product-form>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/product/js/products/form.js') }}"></script>
@endsection