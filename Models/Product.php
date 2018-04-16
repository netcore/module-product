<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Modules\Category\Models\Category;
use Modules\Product\PassThroughs\Product\Format;
use Modules\Product\PassThroughs\Product\Management;
use Modules\Product\Traits\Models\TranslationHelpers;

class Product extends Model
{
    use Translatable,
        TranslationHelpers;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__products';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_variable',
        'parent_id',
    ];

    /**
     * Attributes that are translatable.
     *
     * @var array
     */
    public $translatedAttributes = [
        'slug',
        'title',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Product belongs to many category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'netcore_product__category_product');
    }

    /**
     * Product has many images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Product has many ratings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(ProductRating::class);
    }

    /**
     * Product has many product fields.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productFields(): HasMany
    {
        return $this->hasMany(ProductField::class);
    }

    /**
     * Product has many prices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /** -------------------- Relations for variable products -------------------- */

    /**
     * Product belongs to the parent product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Product has many child products/variations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /** -------------------- Pass throughs -------------------- */

    /**
     * Management pass-trough.
     *
     * @return \Modules\Product\PassThroughs\Product\Management
     */
    public function management(): Management
    {
        return new Management($this);
    }

    /**
     * Format pass-through.
     *
     * @return \Modules\Product\PassThroughs\Product\Format
     */
    public function format(): Format
    {
        return new Format($this);
    }

    /** -------------------- Helpers -------------------- */

    /**
     * Get product cover image.
     *
     * @param string $style
     * @return string
     */
    public function getCoverImage(string $style = 'thumb'): string
    {
        $coverImage = $this->images->where('is_cover', true)->first();

        if (!$coverImage) {
            $coverImage = $this->images->first() ?? new ProductImage;
        }

        return $coverImage->image->url($style);
    }
}