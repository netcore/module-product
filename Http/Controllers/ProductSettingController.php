<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Country\Models\Currency;
use Modules\Product\Models\ProductSetting;

class ProductSettingController extends Controller
{
    /**
     * Update product settings.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse]
     */
    public function update(Request $request): JsonResponse
    {
        // Update currencies.
        foreach ($request->input('currencies', []) as $data) {
            $currencyModel = Currency::findOrFail($data['id']);

            ProductSetting::updateOrCreate([
                'configurable_id'   => $currencyModel->id,
                'configurable_type' => get_class($currencyModel),
                'key'               => 'vat',
            ], [
                'value' => $data['vat'] ?? 21,
            ]);
        }

        return response()->json([
            'success' => 'Settings updated!',
        ]);
    }
}