<?php

namespace Modules\Product\Repositories;

use Modules\Product\Models\Cart\Cart;
use Modules\Product\Models\Product;

class CartRepository
{
    /**
     * Cart model instance.
     *
     * @var Cart
     */
    protected $cart;

    /**
     * CartRepository constructor.
     */
    public function __construct()
    {
        if (auth()->check()) {
            $this->cart = Cart::firstOrCreate([
                'user_id' => auth()->id(),
            ]);
        } else {
            $this->cart = Cart::firstOrCreate([
                'session_id' => session()->getId(),
            ]);
        }
    }

    /**
     * Get cart instance.
     *
     * @return \Modules\Product\Models\Cart\Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * Add item to the cart.
     *
     * @param \Modules\Product\Models\Product $product
     * @param int $quantity
     * @return bool
     */
    public function addItem(Product $product, int $quantity = 1): bool
    {
        $item = $this->cart->items()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        $item->quantity += $quantity;

        return $item->save();
    }

}