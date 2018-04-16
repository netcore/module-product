<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'value_from',
        'value_to',
        'iconable_attributes',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'translations',
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

    /** -------------------- PassThroughs -------------------- */

    /**
     * Format pass-through.
     *
     * @return \Modules\Product\PassThroughs\Parameter\Format
     */
    public function format(): Format
    {
        return new Format($this);
    }
}