@extends('admin::layouts.master')

@section('content')
    @include('product::partials._head', [
        'breadcrumb' => 'admin.products.fields',
        'title'      => 'Product fields',
        'icon'       => 'fa fa-pencil',
    ])

    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="panel-title">List of product fields</span>
            <div class="panel-heading-btn">
                <a href="{{ route('product::fields.create') }}" class="btn btn-xs btn-success">
                    <i class="fa fa-plus-circle"></i> Create
                </a>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-bordered" id="fields-datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Is translatable</th>
                        <th>Is global</th>
                        <th>Type</th>
                        <th>Categories</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/product/js/fields/index.js') }}"></script>
@endsection