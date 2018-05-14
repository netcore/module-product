<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFieldTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__product_field_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value',
        'locale',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Product field translation belongs to product field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productField(): BelongsTo
    {
        return $this->belongsTo(ProductField::class);
    }
}