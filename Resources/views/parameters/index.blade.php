@extends('admin::layouts.master')

@section('content')
    @include('product::partials._head', [
        'breadcrumb' => 'admin.products.parameters',
        'title'      => 'Product parameters',
        'icon'       => 'fa fa-sliders',
    ])

    <div class="panel">
        <div class="panel-heading">
            <span class="panel-title">List of product parameters</span>
            <div class="panel-heading-controls">
                <a href="{{ route('product::parameters.create') }}" class="btn btn-xs btn-success">
                    <i class="fa fa-plus"></i> Create parameter
                </a>
            </div>
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered" id="parameters-datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/product/js/parameters/index.js') }}"></script>
@endsection