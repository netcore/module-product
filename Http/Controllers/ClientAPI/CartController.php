<?php

namespace Modules\Product\Http\Controllers\ClientAPI;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Cart\CartItem;
use Modules\Product\Models\Product;

class CartController extends Controller
{
    /**
     * Get cart and wishlist items.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItems(): JsonResponse
    {
        return response()->json(
            cart()->getItemsWithTotals()
        );
    }

    /**
     * Add product to the cart.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertItem(Product $product): JsonResponse
    {
        $quantity = (int)request('quantity', 0);
        $attribute = (int)request('attribute', 0);

        cart()->insertInCart($product, $quantity, $attribute);

        return response()->json([
            'state' => 'success',
        ]);
    }

    /**
     * Remove product from the cart.
     *
     * @param \Modules\Product\Models\Cart\CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem(CartItem $cartItem): JsonResponse
    {
        cart()->removeFromCart($cartItem);

        return response()->json([
            'state' => 'success',
        ]);
    }

    /**
     * Update cart item quantity.
     *
     * @param \Modules\Product\Models\Cart\CartItem $cartItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuantity(CartItem $cartItem): JsonResponse
    {
        $quantity = (int)request('quantity');

        if ($quantity < 1 || $quantity > $cartItem->availableQuantity) {
            abort(403, 'Available quantity exceeded!');
        }

        cart()->updateItemQuantity($cartItem, $quantity);

        return response()->json([
            'state' => 'success',
        ]);
    }
}