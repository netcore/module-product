<?php

namespace Modules\Product\Traits\Controllers;

use DataTables;
use Illuminate\Http\JsonResponse;
use Modules\Product\Models\Product;

trait ProductsPagination
{
    /**
     * Paginate existing products.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function paginate(): JsonResponse
    {
        $query = Product::with('categories', 'images', 'translations');
        $query->whereIsVariable(false);

        $query->leftJoin('netcore_product__product_translations as translations', function ($join) {
            return $join->on('translations.product_id', '=', 'netcore_product__products.id');
        });

        $query->groupBy('translations.product_id');
        $query->select('netcore_product__products.*');

        $datatable = DataTables::of(
            $query
        );

        $datatable->addColumn('image', function(Product $product) {
            return view('product::products.tds.image', compact('product'))->render();
        });

        $datatable->editColumn('title', function(Product $product) {
            return view('product::products.tds.title', compact('product'))->render();
        });

        $datatable->addColumn('actions', function(Product $product) {
            return view('product::products.tds.actions', compact('product'))->render();
        });

        $datatable->editColumn('parent_id', function (Product $product) {
            return view('product::products.tds.is-variant', compact('product'));
        });

        $datatable->escapeColumns(false);

        return $datatable->make(true);
    }
}