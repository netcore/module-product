@extends('admin::layouts.master')

@php
    $route = isset($field) ? route('product::fields.update', $field) : route('product::fields.store');
    $fieldJson = isset($field) ? $field->formattedForFrontend() : 'null';
@endphp

@section('content')
    @include('product::partials._head', [
        'title'          => 'Product fields',
        'icon'           => 'fa fa-pencil',
        'breadcrumb'     => 'admin.products.fields.' . (isset($field) ? 'edit' : 'create'),
        'breadcrumbData' => $field ?? null,
    ])

    <div id="field-app">
        <field-form
                route="{{ $route }}"
                :types="{{ $types }}"
                :field="{{ $fieldJson }}"
                :languages="{{ $languages }}"
                :categories="{{ $categories }}">
        </field-form>
    </div>
@endsection

@section('scripts')
    <script src="{{ versionedAsset('assets/product/js/fields/form.js') }}"></script>
@endsection