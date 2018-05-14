<?php

namespace Modules\Product\Http\Controllers\ClientAPI;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;

class RatingController extends Controller
{
    /**
     * Submit product rate.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitRate(Product $product): JsonResponse
    {
        $rate = (int)request('rate');

        if ($product->isRated() || ($rate < 1 || $rate > 5)) {
            abort(403);
        }

        $product->rate($rate);

        return response()->json([
            'rate' => $product->average_rating
        ]);
    }
}