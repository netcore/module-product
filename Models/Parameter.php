<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Category\Models\Category;
use Modules\Product\PassThroughs\Parameter\Format;
use Modules\Product\Traits\Models\TranslationHelpers;

class Parameter extends Model
{
    use Translatable, TranslationHelpers;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__parameters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'postfix',
        'value_to',
        'value_from',
        'is_price',
        'is_countable',
        'display_as_range',
        'display_as_detail',
        'iconable_attributes',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'translations',
        'attributes',
    ];

    /**
     * Attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = [
        'name',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Parameter has many attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(ParameterAttribute::class);
    }

    /**
     * Parameter belongs to many categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'netcore_product__category_parameter');
    }

    /** -------------------- PassThroughs -------------------- */

    /**
     * Format pass-through.
     *
     * @return \Modules\Product\PassThroughs\Parameter\Format|mixed
     */
    public function format(): Format
    {
        return new Format($this);
    }
}