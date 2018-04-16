@extends('admin::layouts.master')

@section('content')
    {{-- Breadcrumbs::render('') --}}

    <div class="page-header">
        <div class="row">
            <div class="col-md-4 text-xs-center text-md-left text-nowrap">
                <h1>
                    <span class="text-muted font-weight-light">
                        <i class="page-header-icon fa fa-shopping-cart"></i>
                        Products
                    </span>
                </h1>
            </div>

            <hr class="page-wide-block visible-xs visible-sm">

            <div class="col-xs-12 width-md-auto width-lg-auto width-xl-auto pull-md-right">
                <a href="#settings-modal" data-toggle="modal" class="btn btn-primary btn-block">
                    <span class="btn-label-icon left fa fa-cogs"></span> Product Settings
                </a>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">List of products</span>
            <div class="panel-heading-btn">
                <a href="{{ route('product::products.create') }}" class="btn btn-xs btn-success">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered" id="products-datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Is translatable</th>
                        <th>Is global</th>
                        <th>Categories</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="settings-app">
        <product-settings
                :currencies="{{ product()->getCurrencies() }}"
                route="{{ route('product::settings.update') }}">
        </product-settings>
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/product/js/products/index.js') }}"></script>
@endsection