<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Modules\Product\Models\Cart\Cart;
use Modules\Product\Models\Cart\CartItem;
use Modules\Product\Models\ParameterAttribute;
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
     * Get current cart instance.
     *
     * @return \Modules\Product\Repositories\CartRepository
     */
    public function getInstance(): CartRepository
    {
        return $this;
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
     * Update cart session.
     *
     * @param string $sessionId
     * @return void
     */
    public function setSession(string $sessionId): void
    {
        $this->cart->update([
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Set cart user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $authenticatable
     * @throws \Exception
     */
    public function setUser(Authenticatable $authenticatable): void
    {
        $existingCart = Cart::whereUserId($authenticatable->getAuthIdentifier())->first();

        // If user has associated cart created previously, we need to move current items to the old cart,
        // and delete this one to keep DB clean.

        if ($existingCart && $this->cart->id !== $existingCart->id) {
            $this->moveItemsToCart($existingCart);
            $this->cart->delete();
            $this->cart = $existingCart;
        } else {
            $this->cart->update([
                'user_id' => $authenticatable->getAuthIdentifier(),
            ]);
        }
    }

    /**
     * Move current cart items to other cart.
     *
     * @param \Modules\Product\Models\Cart\Cart $cart
     * @return void
     */
    protected function moveItemsToCart(Cart $cart): void
    {
        foreach ($this->cart->items as $item) {
            $currentItem = $cart->items()->firstOrCreate([
                'product_id'   => $item->product_id,
                'attribute_id' => $item->attribute_id,
            ]);

            $currentItem->quantity = $currentItem->quantity + $item->quantity;
            $currentItem->save();
        }
    }

    /**
     * Add item to the cart.
     *
     * @param \Modules\Product\Models\Product $product
     * @param int $quantity
     * @param \Modules\Product\Models\ParameterAttribute|int|null $attribute
     * @return bool
     */
    public function insertInCart(Product $product, int $quantity = 1, $attribute = null): bool
    {
        if ($quantity < 1) {
            $quantity = 1;
        }

        // Simple product without selectable parameter.
        if (!$product->hasCountableParameters()) {
            if ($quantity > $product->quantity) {
                $quantity = $product->quantity;
            }

            $cartItem = $this->cart->items()->firstOrCreate([
                'product_id' => $product->id,
            ]);

            $cartItem->quantity += $quantity;

            return $cartItem->save();
        }

        // Product with selectable parameter.
        if (!$attribute instanceof ParameterAttribute) {
            $attribute = $product->enabledAttributes()->find($attribute);
        }

        if (!$attribute || !$attribute->exists || !$attribute->pivot->quantity) {
            return false;
        }

        if ($attribute->pivot->quantity < $quantity) {
            $quantity = $attribute->pivot->quantity;
        }

        $cartItem = $this->cart->items()->firstOrCreate([
            'product_id'   => $product->id,
            'attribute_id' => $attribute->id,
        ]);

        $cartItem->quantity += $quantity;

        return $cartItem->save();
    }

    /**
     * Remove item from the cart.
     *
     * @param \Modules\Product\Models\Cart\CartItem $cartItem
     * @return bool
     */
    public function removeFromCart(CartItem $cartItem)
    {
        try {
            return $cartItem->delete();
        } catch (Exception $e) {
            logger()->critical('[API\CartController] Unable to remove cart item - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cart items mapped.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getItems(): Collection
    {
        return $this->cart->items->map(function (CartItem $cartItem) {
            return $cartItem->format()->formatForClientCart();
        });
    }

    /**
     * Determine if cart has items.
     *
     * @return bool
     */
    public function hasItems(): bool
    {
        return !!$this->getItems()->count();
    }

    /**
     * Get items with calculated amounts.
     *
     * @return array
     */
    public function getItemsWithTotals(): array
    {
        $items = $this->getItems();
        $vat = product()->getCurrency()->get('vat', 21);

        $totalPriceWithoutDiscount = 0;
        $totalPriceWithDiscount = 0;

        foreach ($items as $item) {
            $totalPriceWithDiscount += ($item['price_with_discount'] ?? 0) * ($item['quantity'] ?? 0);
            $totalPriceWithoutDiscount += ($item['price_without_discount'] ?? 0) * ($item['quantity'] ?? 0);
        }

        $discountAmount = $totalPriceWithoutDiscount - $totalPriceWithDiscount;

        $withoutVat = $totalPriceWithDiscount / (1 + $vat * 0.01);
        $vatAmount = $totalPriceWithDiscount - $withoutVat;

        return [
            'total_without_vat'   => (float)number_format($withoutVat, 2, '.', ''),
            'vat_amount'          => (float)number_format($vatAmount, 2, '.', ''),
            'discount_amount'     => (float)number_format($discountAmount, 2, '.', ''),
            'total_with_discount' => (float)number_format($totalPriceWithDiscount, 2, '.', ''),
            'products'            => $items,
        ];
    }

    /**
     * Update cart item quantity.
     *
     * @param \Modules\Product\Models\Cart\CartItem $cartItem
     * @param int $quantity
     * @return bool
     */
    public function updateItemQuantity(CartItem $cartItem, int $quantity): bool
    {
        return $cartItem->update([
            'quantity' => $quantity,
        ]);
    }

    /**
     * Empty cart.
     *
     * @return void
     */
    public function deleteAllItems(): void
    {
        $this->cart->items()->delete();
    }
}