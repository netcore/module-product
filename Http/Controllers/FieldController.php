<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Modules\Product\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Http\Requests\FieldRequest;
use Modules\Product\Traits\Controllers\FieldsPagination;

class FieldController extends Controller
{
    use FieldsPagination;

    /**
     * Get product field data.
     *
     * @param \Modules\Product\Models\Field $field
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function show(Field $field): JsonResponse
    {
        return response()->json(
            $field->format()->forSingleAdminSideField()
        );
    }

    /**
     * Store product field in database.
     *
     * @param \Modules\Product\Http\Requests\FieldRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FieldRequest $request): JsonResponse
    {
        $field = Field::create(
            $request->only('type', 'is_translatable', 'is_global')
        );

        $field->storeTranslations(
            $request->input('translations', [])
        );

        $field->categories()->sync(
            $request->input('categories', [])
        );

        foreach ($request->input('options', []) as $data) {
            $field->options()->create([])->storeTranslations(
                array_get($data, 'translations', [])
            );
        }

        return response()->json([
            'success'  => 'Field created successfully!',
            'redirect' => [
                'name'   => 'fields.edit',
                'params' => [
                    'id' => $field->id,
                ],
            ],
        ]);
    }

    /**
     * Update product field in database.
     *
     * @param \Modules\Product\Http\Requests\FieldRequest $request
     * @param \Modules\Product\Models\Field $field
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FieldRequest $request, Field $field): JsonResponse
    {
        $field->update(
            $request->only('is_translatable', 'is_global')
        );

        $field->updateTranslations(
            $request->input('translations', [])
        );

        $field->categories()->sync(
            $request->input('categories', [])
        );

        $existingOptionIds = $field->options->pluck('id')->toArray();
        $currentOptionIds = [];

        foreach ($request->input('options', []) as $item) {
            if ($id = array_get($item, 'id')) {
                $field->options()->findOrFail($id)->updateTranslations(
                    array_get($item, 'translations', [])
                );

                $currentOptionIds[] = $id;
            } else {
                $option = $field->options()->create([]);
                $option->storeTranslations(
                    array_get($item, 'translations', [])
                );
            }
        }

        // Find out options that should be deleted.
        $optionsToDelete = array_diff($existingOptionIds, $currentOptionIds);
        $field->options()->whereIn('id', $optionsToDelete)->delete();

        return response()->json([
            'success' => 'Product field successfully updated!',
        ]);
    }

    /**
     * Delete product field from database.
     *
     * @param \Modules\Product\Models\Field $field
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Field $field): JsonResponse
    {
        try {
            $field->delete();
        } catch (Exception $e) {
            abort(500, $e->getMessage());
        }

        return response()->json([
            'message' => 'Field successfully deleted!',
        ]);
    }
}