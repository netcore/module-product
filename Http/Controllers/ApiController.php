<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Category\Models\Category;
use Modules\Product\Models\Field;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\FieldsRepository;
use Netcore\Translator\Models\Language;

class ApiController extends Controller
{
    /**
     * Application languages.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $languages;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->languages = languages();
    }

    /**
     * Get fields of selected categories.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Product\Repositories\FieldsRepository $repository
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductFields(Request $request, FieldsRepository $repository): JsonResponse
    {
        return response()->json(
            $repository->getFieldsOfCategories(
                $request->input('categories', [])
            )
        );
    }
}