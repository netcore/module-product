<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Category\Models\Category;
use Modules\Product\PassThroughs\Field\Format;

class Field extends Model
{
    use Translatable, SyncTranslations;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_translatable',
        'is_global',
        'type',
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
     * Available field types.
     */
    public const TYPES = [
        'text' => [
            'name'         => 'Text',
            'translatable' => true, // can be translatable?
        ],

        'textarea' => [
            'name'         => 'Textarea',
            'translatable' => true, // can be translatable?
        ],

        'checkbox' => [
            'name' => 'Checkbox',
        ],

        'radio' => [
            'name' => 'Radio',
        ],

        'number' => [
            'name' => 'Number',
        ],

        'date' => [
            'name' => 'Date',
        ],
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Product field belongs to many categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'netcore_product__category_field');
    }

    /**
     * Field has many options. (used for radio input)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options(): HasMany
    {
        return $this->hasMany(FieldOption::class);
    }

    /** -------------------- Pass-throughs -------------------- */

    /**
     * Format pass-through.
     *
     * @return \Modules\Product\PassThroughs\Field\Format
     */
    public function format(): Format
    {
        return new Format($this);
    }
}