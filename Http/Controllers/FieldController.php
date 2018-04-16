<?php

namespace Modules\Product\Http\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

use Modules\Product\Http\Requests\FieldRequest;
use Modules\Product\Models\Field;
use Modules\Product\Traits\Controllers\FieldsPagination;

class FieldController extends Controller
{
    use FieldsPagination;

    /**
     * Display listing of product fields.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('product::fields.index', compact('fields'));
    }

    /**
     * Display product field create form.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('product::fields.form');
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
            'redirect' => route('product::fields.edit', $field),
        ]);
    }

    /**
     * Display product field edit form.
     *
     * @param \Modules\Product\Models\Field $field
     * @return \Illuminate\View\View
     */
    public function edit(Field $field): View
    {
        return view('product::fields.form', compact('field'));
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
            'field'   => $field->refresh()->formattedForFrontend(),
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
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Field successfully deleted!',
        ]);
    }
}