<?php

namespace Modules\Product\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Traits\BootStapler;
use Modules\Admin\Traits\StaplerAndTranslatable;
use Modules\Product\Traits\Models\TranslationHelpers;

class ParameterAttribute extends Model implements StaplerableInterface
{
    use StaplerAndTranslatable;
    use BootStapler;
    use TranslationHelpers;

    use Translatable {
        StaplerAndTranslatable::getAttribute insteadof Translatable;
        StaplerAndTranslatable::setAttribute insteadof Translatable;
    }

    use EloquentTrait {
        StaplerAndTranslatable::getAttribute insteadof EloquentTrait;
        StaplerAndTranslatable::setAttribute insteadof EloquentTrait;
        BootStapler::boot insteadof EloquentTrait;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__parameter_attributes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image',
    ];

    /**
     * Stapler configuration.
     *
     * @var array
     */
    protected $staplerConfig = [
        'image' => [
            'url'           => '/uploads/products/product_images/:attachment/:id_partition/:style/:filename',
            'default_url'   => '//placehold.it/150',
            'default_style' => 'thumb',
            'styles'        => [
                'large'  => '500x500',
                'medium' => '250x250',
                'thumb'  => '75x75',
            ],
        ],
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
     * Attribute belongs to many parameters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parameter(): BelongsTo
    {
        return $this->belongsTo(Parameter::class);
    }
}