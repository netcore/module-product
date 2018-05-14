<?php

namespace Modules\Product\Http\Controllers\ClientAPI;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Models\ShippingOption;

class ShippingOptionController extends Controller
{
    /**
     * Get available shipping options.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $options = ShippingOption::with('locations')->whereIsActive(true)->get();

        $response = $options->map(function (ShippingOption $shippingOption) {
            return $shippingOption->format()->forClientSide();
        });

        return response()->json($response);
    }
}