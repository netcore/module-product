<?php

namespace Modules\Product\Traits\Controllers;

use DataTables;
use Illuminate\Http\JsonResponse;
use Modules\Product\Models\Field;

trait FieldsPagination
{
    /**
     * Paginate existing product fields.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paginate(): JsonResponse
    {
        $fieldsQuery = Field::with('categories', 'translations')->select('netcore_product__fields.*');

        $fieldsQuery->leftJoin('netcore_product__field_translations as translations', function ($join) {
            return $join->on('translations.field_id', '=', 'netcore_product__fields.id');
        });

        $fieldsQuery->groupBy('translations.field_id');

        $datatable = DataTables::eloquent(
            $fieldsQuery
        );

        $datatable->editColumn('name', function (Field $productField) {
            return view('product::fields.tds.name', compact('productField'))->render();
        });

        $datatable->editColumn('is_global', function (Field $productField) {
            list($className, $text) = $productField->is_global ? ['success', 'Yes'] : ['default', 'No'];
            return "<span class=\"badge badge-{$className}\">{$text}</span>";
        });

        $datatable->editColumn('is_translatable', function (Field $productField) {
            list($className, $text) = $productField->is_translatable ? ['success', 'Yes'] : ['default', 'No'];
            return "<span class=\"badge badge-{$className}\">{$text}</span>";
        });

        $datatable->editColumn('type', function (Field $productField) {
            return ucfirst($productField->type);
        });

        $datatable->addColumn('categories', function (Field $productField) {
            return view('product::fields.tds.categories', compact('productField'))->render();
        });

        $datatable->addColumn('actions', function (Field $productField) {
            return view('product::fields.tds.actions', compact('productField'))->render();
        });

        $datatable->escapeColumns(false);

        return $datatable->make(true);
    }
}