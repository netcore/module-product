<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class ProductSetting extends Model
{
    /**
     * Cached settings.
     *
     * @var Collection
     */
    static $cachedSettings;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'configurable_id',
        'configurable_type',
        'key',
        'value',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Configurable relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function configurable(): MorphTo
    {
        return $this->morphTo();
    }

    /** -------------------- Helpers -------------------- **/

    /**
     * Get cached settings.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getCachedSettings(): Collection
    {
        if (!static::$cachedSettings) {
            static::$cachedSettings = static::all();
        }

        return static::$cachedSettings;
    }
}