<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Modules\Admin\Traits\SyncTranslations;
use Modules\Product\PassThroughs\ProductField\Format;
use Modules\Product\Traits\Models\FieldTranslatable;

class ProductField extends Model
{
    use Translatable, SyncTranslations;

    use FieldTranslatable {
        FieldTranslatable::setAttribute insteadof Translatable;
        FieldTranslatable::getAttribute insteadof Translatable;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__product_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_id',
        'value',
    ];

    /**
     * Attributes that are translatable.
     *
     * @var array
     */
    protected $translatedAttributes = [
        'value',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Product field belongs to field.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /** -------------------- Accessors -------------------- */

    public function getFieldNameAttribute(): string
    {
        return $this->field->name ?? '';
    }

    public function getFieldValueAttribute()
    {
        if (!$this->field->is_translatable) {
            return $this->value;
        }

        return $this->getAttribute('value');
    }

    /** -------------------- Pass throughs -------------------- */

    /**
     * Format pass-through.
     *
     * @return \Modules\Product\PassThroughs\ProductField\Format
     */
    public function format(): Format
    {
        return new Format($this);
    }
}