<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Product\Traits\Models\TranslationHelpers;
use Modules\Product\PassThroughs\ShippingOption\Format;

class ShippingOption extends Model
{
    use Translatable, TranslationHelpers;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__shipping_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'price',
        'is_active',
        'is_free_enabled',
        'price_when_free',
    ];

    /**
     * Attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = [
        'name',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'translations',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Shipping options has many locations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations(): HasMany
    {
        return $this->hasMany(ShippingOptionLocation::class);
    }

    /** -------------------- Pass-throughs -------------------- */

    /**
     * Format pass-through.
     *
     * @return \Modules\Product\PassThroughs\ShippingOption\Format
     */
    public function format(): Format
    {
        return new Format($this);
    }
}