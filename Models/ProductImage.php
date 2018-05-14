<?php

namespace Modules\Product\Models;

use Codesleeve\Stapler\ORM\EloquentTrait;
use Codesleeve\Stapler\ORM\StaplerableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Admin\Traits\BootStapler;
use Modules\Product\Helpers\GlobalHelpers;

class ProductImage extends Model implements StaplerableInterface
{
    use EloquentTrait;
    use BootStapler {
        BootStapler::boot insteadof EloquentTrait;
    }

    /**
     * ProductImage constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $staplerConfig = config('netcore.module-product.product_image_config', [
            'url'           => '/uploads/products/product_images/:attachment/:id_partition/:style/:filename',
            'default_url'   => '//placehold.it/150',
            'default_style' => 'thumb',
            'styles'        => [
                'thumb'  => '150x150',
                'medium' => '300x300',
                'large'  => '500x500',
                'full'   => '1000x1000',
            ],
        ]);

        $this->hasAttachedFile('image', $staplerConfig);

        parent::__construct($attributes);
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__product_images';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_preview',
        'image',
        'order',
    ];

    /**
     * Stapler config.
     *
     * @var array
     */
    protected $staplerConfig = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /** -------------------- Accessors -------------------- */

    /**
     * Convert bytes to human readable units.
     *
     * @return string
     */
    public function getHumanReadableImageSizeAttribute(): string
    {
        return GlobalHelpers::convertToMb($this->image_file_size);
    }

    /** -------------------- Relations -------------------- */

    /**
     * Product image belongs to the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}