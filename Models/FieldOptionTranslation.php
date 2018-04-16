<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Netcore\Translator\Models\Language;

class FieldOptionTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__field_option_translations';

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
     * Field option translation belongs to the field option.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fieldOption(): BelongsTo
    {
        return $this->belongsTo(FieldOption::class);
    }

    /**
     * Field option translation belongs to the language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'locale', 'iso_code');
    }
}