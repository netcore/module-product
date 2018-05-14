<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

use Modules\Category\Models\Category;
use Modules\Product\Models\Field;
use Modules\Product\Repositories\CategoriesRepository;
use Modules\Product\Repositories\FieldsRepository;

class BaseController extends Controller
{
    /**
     * Get category related data.
     *
     * @param \Modules\Product\Repositories\FieldsRepository $fieldsRepository
     * @param \Modules\Product\Repositories\CategoriesRepository $categoriesRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryData(FieldsRepository $fieldsRepository, CategoriesRepository $categoriesRepository): JsonResponse
    {
        $categories = (array)request('categories', []);

        return response()->json([
            'fields'     => $fieldsRepository->getFieldsOfCategories($categories),
            'parameters' => $categoriesRepository->getParametersWithAttributes($categories),
        ]);
    }

    /**
     * Get initial data.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function init(): JsonResponse
    {
        $languages = languages();
        $fieldTypes = Field::TYPES;
        $currencies = product()->getCurrencies();

        $categories = product()->getCategoryGroup()->categories()->with('translations', 'ancestors.translations')->get();
        $categories = $categories->map(function (Category $category) {
            return [
                'id'   => $category->id,
                'text' => $category->chainedName,
            ];
        });

        return response()->json([
            'languages'  => $languages,
            'currencies' => $currencies,
            'categories' => $categories,
            'fieldTypes' => $fieldTypes,
        ]);
    }
}