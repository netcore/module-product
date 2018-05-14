<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Http\Requests\ProductRequest;
use Modules\Product\Models\Product;
use Modules\Product\Traits\Controllers\ProductsPagination;

class ProductController extends Controller
{
    use ProductsPagination;

    /**
     * Fetch product data for frontend.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json(
            $product->format()->forAdminSideVue()
        );
    }

    /**
     * Store a newly created product in storage.
     *
     * @param \Modules\Product\Http\Requests\ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create(
            $request->only('is_variable')
        );

        product()->updateProduct($product, $request->all());

        return response()->json([
            'success'  => 'Product successfully created!',
            'redirect' => [
                'name'   => 'products.edit',
                'params' => [
                    'id' => $product->id,
                ],
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Modules\Product\Http\Requests\ProductRequest $request
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        product()->updateProduct($product, $request->all());

        return response()->json([
            'success' => 'Product successfully updated!',
            'product' => $product->fresh()->format()->forAdminSideVue(),
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
        try {
            $product->delete();
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return response()->json([
            'success' => 'Product successfully deleted!',
        ]);
    }
}