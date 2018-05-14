<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Modules\Product\Http\Requests\ParameterRequest;
use Modules\Product\Models\Parameter;
use Modules\Product\Traits\Controllers\ParametersPagination;

class ParameterController extends Controller
{
    use ParametersPagination;

    /**
     * Fetch parameter data.
     *
     * @param \Modules\Product\Models\Parameter $parameter
     * @return \Illuminate\Support\Collection
     */
    public function show(Parameter $parameter): Collection
    {
        return $parameter->format()->forAdminSideVue();
    }

    /**
     * Store parameter in the database.
     *
     * @param \Modules\Product\Http\Requests\ParameterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ParameterRequest $request): JsonResponse
    {
        $parameter = Parameter::create(
            $request->except('translations', 'attributes')
        );

        $parameter->syncTranslations(
            $request->input('translations', [])
        );

        $parameter->categories()->sync(
            $request->input('categories', [])
        );

        foreach (array_get($request->all(), 'attributes', []) as $row) {
            $attribute = $parameter->attributes()->create(
                array_except($row, 'translations')
            );

            $attribute->syncTranslations(
                array_get($row, 'translations', [])
            );
        }

        return response()->json([
            'redirect' => [
                'name'   => 'parameters.edit',
                'params' => [
                    'id' => $parameter->id,
                ],
            ],
            'success'  => 'Parameter successfully created!',
        ]);
    }

    /**
     * Update parameter in database.
     *
     * @param \Modules\Product\Http\Requests\ParameterRequest $request
     * @param \Modules\Product\Models\Parameter $parameter
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ParameterRequest $request, Parameter $parameter): JsonResponse
    {
        $parameter->update(
            $request->except('translations', 'attributes', 'categories')
        );

        $parameter->syncTranslations(
            $request->input('translations', [])
        );

        $parameter->categories()->sync(
            $request->input('categories', [])
        );

        $existingAttributeIds = $parameter->attributes->pluck('id')->toArray();
        $currentAttributeIds = [];

        foreach (array_get($request->all(), 'attributes', []) as $row) {
            if (isset($row['id'])) {
                $attribute = $parameter->attributes()->findOrFail($row['id']);
                $attribute->update(
                    array_only($row, 'image')
                );
            } else {
                $attribute = $parameter->attributes()->create(
                    array_only($row, 'image')
                );
            }

            $currentAttributeIds[] = $attribute->id;

            $attribute->syncTranslations(
                array_get($row, 'translations', [])
            );
        }

        $attributesToDelete = array_diff($existingAttributeIds, $currentAttributeIds);
        $parameter->attributes()->whereIn('id', $attributesToDelete)->delete();

        return response()->json([
            'success' => 'Parameter successfully updated!',
        ]);
    }

    /**
     * Delete parameter from database.
     *
     * @param \Modules\Product\Models\Parameter $parameter
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Parameter $parameter): JsonResponse
    {
        try {
            $parameter->delete();
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json([
            'message' => 'Parameter successfully deleted!',
        ]);
    }
}