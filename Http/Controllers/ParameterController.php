<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Product\Http\Requests\ParameterRequest;
use Modules\Product\Models\Parameter;
use Modules\Product\Traits\Controllers\ParametersPagination;

class ParameterController extends Controller
{
    use ParametersPagination;

    /**
     * Display listing of parameters.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('product::parameters.index');
    }

    /**
     * Display parameter create form.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('product::parameters.form');
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

        foreach (array_get($request->all(), 'attributes', []) as $row) {
            $attribute = $parameter->attributes()->create(
                array_except($row, 'translations')
            );

            $attribute->syncTranslations(
                array_get($row, 'translations', [])
            );
        }

        return response()->json([
            'redirect' => route('product::parameters.edit', $parameter),
            'success'  => 'Parameter successfully created!',
        ]);
    }

    /**
     * Display parameter edit form.
     *
     * @param \Modules\Product\Models\Parameter $parameter
     * @return \Illuminate\View\View
     */
    public function edit(Parameter $parameter): View
    {
        return view('product::parameters.form', compact('parameter'));
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
        dd($request->all());
    }

}