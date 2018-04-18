<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Category\Models\CategoryGroup;
use Modules\Product\Repositories\FieldsRepository;

class BaseController extends Controller
{
    /**
     * Get available languages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function languages(): JsonResponse
    {
        return response()->json(
            languages()
        );
    }

    /**
     * Get available currencies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function currencies(): JsonResponse
    {
        return response()->json(
            product()->getCurrencies()
        );
    }

    /**
     * Get product fields.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Product\Repositories\FieldsRepository $repository
     * @return \Illuminate\Http\JsonResponse
     */
    public function productFields(Request $request, FieldsRepository $repository): JsonResponse
    {
        return response()->json(
            $repository->getFieldsOfCategories(
                $request->input('categories', [])
            )
        );
    }

    /**
     * Get available product categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(): JsonResponse
    {
        // Categories.
        $categoryGroup = CategoryGroup::where([
            'key' => config('netcore.module-product.used_category_group.key'),
        ])->first();

        if (!$categoryGroup) {
            $categoryGroup = new CategoryGroup;
        }

        $categories = $categoryGroup->categories()->with('ancestors')->get();
        $categories = $categories->map(function ($category) {
            return [
                'id'   => $category->id,
                'text' => $category->chainedName,
            ];
        });

        return response()->json($categories);
    }
}