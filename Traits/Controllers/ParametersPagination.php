<?php

namespace Modules\Product\Traits\Controllers;

use DataTables;
use Modules\Product\Models\Parameter;

trait ParametersPagination
{
    /**
     * Paginate existing product parameters.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paginate()
    {
        $query = Parameter::query();

        $query->leftJoin('netcore_product__parameter_translations as translations', function ($join) {
            return $join->on('translations.parameter_id', '=', 'netcore_product__parameters.id');
        });

        $query->groupBy('translations.parameter_id');
        $query->select('netcore_product__parameters.*');

        $datatable = DataTables::of(
            $query
        );

        $datatable->editColumn('name', function(Parameter $parameter) {
            return view('product::parameters.tds.name', compact('parameter'));
        });

        $datatable->addColumn('actions', function(Parameter $parameter) {
            return view('product::parameters.tds.actions', compact('parameter'));
        });

        $datatable->escapeColumns(false);

        return $datatable->make(true);
    }
}