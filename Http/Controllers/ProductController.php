<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductSetting;
use Modules\Product\Repositories\ProductsRepository;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('product::products.index');
    }

    /**
     * Fetch product data for frontend.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'product' => $product->format()->forAdminSideVue(),
        ]);
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('product::products.form');
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  Request $request
     * @param \Modules\Product\Repositories\ProductsRepository $repository
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ProductsRepository $repository): JsonResponse
    {
        $product = Product::create(
            $request->only('is_variable')
        );

        $repository->updateProduct(
            $product, $request->all()
        );

        return response()->json([
            'success'  => 'Product successfully created!',
            'redirect' => route('product::products.edit', $product),
        ]);
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product): View
    {
        return view('product::products.form', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param \Modules\Product\Models\Product $product
     * @param \Modules\Product\Repositories\ProductsRepository $repository
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product, ProductsRepository $repository): JsonResponse
    {
        $repository->updateProduct(
            $product, $request->all()
        );

        return response()->json([
            'success' => 'Product successfully updated!',
            'refresh' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {

    }
}
