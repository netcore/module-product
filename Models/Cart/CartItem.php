<?php

namespace Modules\Product\Models\Cart;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Models\ParameterAttribute;
use Modules\Product\Models\Product;
use Modules\Product\PassThroughs\CartItem\Format;

class CartItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'netcore_product__cart_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'attribute_id',
        'quantity',
    ];

    /** -------------------- Relations -------------------- */

    /**
     * Cart item belongs to the cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Cart item belongs to the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Cart item belongs to the parameter attribute.
     * This relation is used in case, when product has variable parameter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ParameterAttribute::class);
    }

    /** -------------------- Pass-throughs -------------------- */

    /**
     * Get the formatter pass-through instance.
     *
     * @return \Modules\Product\PassThroughs\CartItem\Format
     */
    public function format(): Format
    {
        return new Format($this);
    }

    /** -------------------- Accessors -------------------- */

    /**
     * Get product available (max) quantity.
     *
     * @return int
     */
    public function getAvailableQuantityAttribute(): int
    {
        if ($this->attribute_id && $attribute = $this->product->enabledAttributes->where('id', $this->attribute_id)->first()) {
            return $attribute->pivot->quantity;
        }

        return $this->product->quantity;
    }
}