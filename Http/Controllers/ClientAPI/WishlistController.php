<?php

namespace Modules\Product\Http\Controllers\ClientAPI;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;
use Modules\Product\Models\Wishlist\WishlistItem;

class WishlistController extends Controller
{
    /**
     * Get items in wishlist.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getItems(): JsonResponse
    {
        try {
            return wishlist()->response();
        } catch (Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Insert item in wishlist.
     *
     * @param \Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertItem(Product $product): JsonResponse
    {
        $attribute = (int)request('attribute');

        wishlist()->insertItem($product, $attribute);

        return response()->json([
            'state' => 'success',
        ]);
    }

    /**
     * Remove item from wishlist.
     *
     * @param \Modules\Product\Models\Wishlist\WishlistItem $wishlistItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItem(WishlistItem $wishlistItem): JsonResponse
    {
        wishlist()->removeItem($wishlistItem);

        return $this->getItems();
    }

    /**
     * Move item to the cart.
     *
     * @param \Modules\Product\Models\Wishlist\WishlistItem $wishlistItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveItemToCart(WishlistItem $wishlistItem): JsonResponse
    {
        if ($wishlistItem->attribute) {
            $attribute = $wishlistItem->product->enabledAttributes->where('id', $wishlistItem->attribute->id)->first();
        } else {
            $attribute = null;
        }

        cart()->insertInCart($wishlistItem->product, 1, $attribute);

        try {
            $wishlistItem->delete();
        } catch (Exception $e) {
            logger()->critical($e->getMessage());
        }

        return $this->getItems();
    }
}