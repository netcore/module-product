<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductImage;

class ProductImageController extends Controller
{
    /**
     * Mark image as preview image.
     *
     * @param \Modules\Product\Models\Product $product
     * @param \Modules\Product\Models\ProductImage $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function markImageAsPreview(Product $product, ProductImage $image): JsonResponse
    {
        $product->images()->update([
            'is_preview' => false,
        ]);

        $image->update([
            'is_preview' => true,
        ]);

        return response()->json([
            'success' => 'Image successfully marked as preview image!',
        ]);
    }

    /**
     * Save images order.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveImagesOrder(Product $product): JsonResponse
    {
        foreach (request('order', []) as $i => $id) {
            $product->images()->whereId($id)->update([
                'order' => $i + 1,
            ]);
        }

        return response()->json([
            'success' => 'Order successfully saved!',
        ]);
    }

    /**
     * Delete product image.
     *
     * @param \Modules\Product\Models\Product $product
     * @param \Modules\Product\Models\ProductImage $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product, ProductImage $image): JsonResponse
    {
        try {
            $image->delete();
        } catch (Exception $e) {
            abort(500, 'Unable to delete image!');
        }

        return response()->json([
            'success' => 'Image successfully deleted!',
        ]);
    }
}