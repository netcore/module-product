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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Stapler config.
     *
     * @var array
     * @TODO - merge sizes config from module config file.
     */
    protected $staplerConfig = [
        'image' => [
            'default_style' => 'thumb',
            'styles'        => [
                'thumb'  => '150x150',
                'medium' => '300x300',
                'large'  => '500x500',
                'full'   => '1000x1000',
            ],
        ],
    ];

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

    /** -------------------- Helpers -------------------- */

    /**
     * Format for response.
     *
     * @param bool $forAdmin
     * @param string $style
     * @return array
     */
    public function formatForResponse(bool $forAdmin = true, string $style = 'thumb'): array
    {
        $image = [
            'id'         => $this->id,
            'image'      => $this->image->url($style),
            'is_preview' => $this->is_preview,
        ];

        if ($forAdmin) {
            $image['info'] = [
                'size'     => GlobalHelpers::convertToMb($this->image_file_size),
                'name'     => $this->image_file_name,
                'modified' => $this->image_updated_at,
            ];

            $image['routes'] = [
                'destroy' => [
                    'route'  => route('product::products.images.destroy', [$this->product, $this]),
                    'method' => 'DELETE',
                ],

                'asPreview' => [
                    'route'  => route('product::products.images.mark-as-preview', [$this->product, $this]),
                    'method' => 'POST',
                ],
            ];
        }

        return $image;
    }
}