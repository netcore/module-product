<?php

namespace Modules\Product\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Category\Models\Category;
use Modules\Product\Models\Parameter;
use Modules\Product\Models\ParameterAttribute;

class CategoriesRepository
{
    /**
     * Get parameters of given categories.
     *
     * @param array $categories
     * @return \Illuminate\Support\Collection
     */
    public function getParametersWithAttributes(array $categories): Collection
    {
        $categoryIds = [];

        foreach ($categories as $category) {
            $ancestorsAndSelf = Category::ancestorsAndSelf($category)->pluck('id')->toArray();
            $categoryIds = array_merge($categoryIds, $ancestorsAndSelf);
        }

        $categoryIds = array_unique($categoryIds);

        $parameters = Parameter::with('attributes')->whereHas('categories', function (Builder $builder) use ($categoryIds) {
            return $builder->whereIn('id', $categoryIds);
        })->get();

        return $parameters->map(Closure::fromCallable([$this, 'mapParameter']));
    }

    /**
     * Parameter map callback.
     *
     * @param \Modules\Product\Models\Parameter $parameter
     * @return array
     */
    private function mapParameter(Parameter $parameter)
    {
        return [
            'id'           => $parameter->id,
            'name'         => $parameter->name,
            'type'         => $parameter->type,
            'value_from'   => $parameter->value_from,
            'value_to'     => $parameter->value_to,
            'is_countable' => $parameter->is_countable,
            'attributes'   => $parameter->attributes->map(function (ParameterAttribute $attribute) {
                return [
                    'id'   => $attribute->id,
                    'name' => $attribute->name,
                ];
            }),
        ];
    }
}