<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Netcore\Translator\Models\Language;

class FieldTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__field_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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
        return $this->belongsTo(Field::class);
    }

    /**
     * Product field translation belongs to language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'locale', 'iso_code');
    }
}