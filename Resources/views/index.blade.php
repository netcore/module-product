@extends('admin::layouts.master')

@section('content')
    <div id="product-app">
        <router-view></router-view>
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/product/js/app.js') }}"></script>
@endsection