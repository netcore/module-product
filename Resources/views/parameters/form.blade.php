@extends('admin::layouts.master')

@php
    $isEdit = isset($parameter);
    $breadcrumb = 'admin.products.parameters.' . ($isEdit ? 'edit' : 'create');
    $json = $isEdit ? $parameter->format()->forAdminSideVue() : 'null';
    $route = $isEdit ? route('product::parameters.update', $parameter) : route('product::parameters.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

@section('content')
    @include('product::partials._head', [
        'breadcrumb'     => $breadcrumb,
        'breadcrumbData' => @$parameter,
        'icon'           => 'fa fa-sliders',
        'title'          => 'Product parameters'
    ])

    <div id="parameter-app">
        <parameter-form
                route="{{ $route }}"
                method="{{ $method }}"
                :parameter="{{ $json }}"
                :languages="{{ languages() }}">
        </parameter-form>
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/product/js/parameters/form.js') }}"></script>
@endsection