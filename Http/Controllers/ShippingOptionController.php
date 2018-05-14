<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Models\ShippingOption;

class ShippingOptionController extends Controller
{
    /**
     * Get existing shipping options.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            ShippingOption::withCount('locations')->get()
        );
    }

    /**
     * Display single option.
     *
     * @param \Modules\Product\Models\ShippingOption $shippingOption
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShippingOption $shippingOption): JsonResponse
    {
        return response()->json(
            $shippingOption->format()->forAdminSide()
        );
    }

    /**
     * Toggle shipping option active state.
     *
     * @param \Modules\Product\Models\ShippingOption $shippingOption
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleState(ShippingOption $shippingOption): JsonResponse
    {
        $shippingOption->update([
            'is_active' => !$shippingOption->is_active,
        ]);

        return response()->json([
            'message' => 'Status updated successfully.',
        ]);
    }

    /**
     * Delete shipping option.
     *
     * @param \Modules\Product\Models\ShippingOption $shippingOption
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ShippingOption $shippingOption): JsonResponse
    {
        // DPD and Omniva options cannot be deleted.
        if ($shippingOption->type === 'dpd' || $shippingOption->type === 'omniva') {
            return response()->json([
                'message' => 'This option can not be deleted!',
            ], 403);
        }

        try {
            $shippingOption->delete();
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Shipping option successfully deleted!',
        ], 500);
    }
}