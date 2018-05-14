<?php

namespace Modules\Product\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Modules\Category\Models\Category;
use Modules\Country\Models\Currency;
use Modules\Product\PassThroughs\Product\Format;
use Modules\Product\PassThroughs\Product\Management;
use Modules\Product\Traits\Models\TranslationHelpers;

class Product extends Model
{
    use Translatable, TranslationHelpers;

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
        'description',
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
     * Product has many ratings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userRating(): HasMany
    {
        $relation = $this->hasMany(ProductRating::class)->where('ip_address', request()->getClientIp());

        if (auth()->check()) {
            $relation->orWhere('user_id', auth()->id());
        }

        return $relation;
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

    /**
     * Product parameter belongs to many parameters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function enabledParameters(): BelongsToMany
    {
        return $this->belongsToMany(Parameter::class, 'netcore_product__parameter_product')->withPivot('value');
    }

    /**
     * Product attribute belongs to many attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function enabledAttributes(): BelongsToMany
    {
        return $this->belongsToMany(ParameterAttribute::class, 'netcore_product__attribute_product')->withPivot('quantity');
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

    /** -------------------- Accessors -------------------- */

    /**
     * Get price attribute.
     *
     * @return float
     */
    public function getPriceAttribute(): float
    {
        return number_format(optional($this->priceModel)->with_vat_with_discount, 2, '.', '');
    }

    /**
     * Get product available quantity.
     *
     * @return int
     */
    public function getQuantityAttribute(): int
    {
        // Show global count, because product doesn't have countable parameter with attributes.
        if (!$this->hasCountableParameters()) {
            return $this->quantity ?? 0;
        }

        $count = 0;

        // Count quantities in countable parameters -> attributes.
        foreach ($this->enabledParameters->where('is_countable', true) as $enabledParameter) {
            foreach ($this->enabledAttributes->where('parameter_id', $enabledParameter->id) as $enabledAttribute) {
                $count += (int)data_get($enabledAttribute, 'pivot.quantity', 0);
            }
        }

        return $count;
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
     * @param null $method
     * @return \Modules\Product\PassThroughs\Product\Format
     */
    public function format($method = null)
    {
        $instance = new Format($this);

        if ($method) {
            return $instance->{$method}();
        }

        return $instance;
    }

    /** -------------------- Helpers -------------------- */

    /**
     * Get product product price for active currency.
     *
     * @return \Modules\Product\Models\ProductPrice
     */
    public function getPriceModelAttribute(): ProductPrice
    {
        static $currency;

        if ($currency) {
            return $this->prices->where('currency_id', $currency->id)->first() ?? new ProductPrice;
        }

        $activeCurrency = session('currency', config('netcore.module-product.default_currency', 'EUR'));

        if (is_int($activeCurrency)) {
            $currency = Currency::find($activeCurrency);
        } else {
            $currency = Currency::whereCode($activeCurrency)->first();
        }

        return $this->prices->where('currency_id', $currency->id)->first() ?? new ProductPrice;
    }

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

    /**
     * Determine if product has countable parameters (and attributes) assigned.
     *
     * @return bool
     */
    public function hasCountableParameters(): bool
    {
        $hasCountableParameters = false;

        foreach ($this->enabledParameters as $enabledParameter) {
            if (!$enabledParameter->is_countable) {
                continue;
            }

            if ($this->enabledAttributes->where('parameter_id', $enabledParameter->id)->count()) {
                $hasCountableParameters = true;
            }
        }

        return $hasCountableParameters;
    }

    /**
     * Determine if user has already submitted rating.
     *
     * @return bool
     */
    public function isRated(): bool
    {
        return !!$this->userRating->count();
    }

    /**
     * Rate product.
     *
     * @param int $rate
     * @return void
     */
    public function rate(int $rate): void
    {
        $this->ratings()->create([
            'user_id'    => auth()->id(),
            'ip_address' => request()->getClientIp(),
            'rate'       => $rate,
        ]);

        $this->average_rating = round($this->ratings()->avg('rate'));
        $this->save();
    }
}