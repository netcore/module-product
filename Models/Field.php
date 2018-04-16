<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\Admin\Traits\SyncTranslations;
use Modules\Category\Models\Category;

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

    /** -------------------- Helpers -------------------- */

    /**
     * Get field data formatted according to frontend requirements (VueJS app).
     *
     * @return \Illuminate\Support\Collection
     */
    public function formattedForFrontend(): Collection
    {
        $data = [
            'type'            => $this->type,
            'is_global'       => $this->is_global,
            'is_translatable' => $this->is_translatable,
            'categories'      => $this->categories->pluck('id'),
            'translations'    => [],
            'options'         => [],
        ];

        foreach (languages() as $language) {
            $data['translations'][$language->iso_code] = [
                'name' => $this->translations->where('locale', $language->iso_code)->first()->name ?? '',
            ];
        }

        foreach ($this->options as $i => $option) {
            $data['options'][$i] = [
                'id'           => $option->id,
                'translations' => [],
            ];

            foreach (languages() as $language) {
                $data['options'][$i]['translations'][$language->iso_code] = [
                    'name' => $option->translations->where('locale', $language->iso_code)->first()->name ?? '',
                ];
            }
        }

        return collect($data);
    }
}