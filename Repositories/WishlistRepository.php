<?php

namespace Modules\Product\Repositories;

use Exception;
use Illuminate\Http\JsonResponse;
use Modules\Product\Models\Product;
use Modules\Product\Models\Wishlist\Wishlist;
use Modules\Product\Models\Wishlist\WishlistItem;

class WishlistRepository
{
    /**
     * Wishlist model instance.
     *
     * @var \Modules\Product\Models\Wishlist\Wishlist
     */
    protected $wishlist;

    /**
     * CartRepository constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        if (auth()->guest()) {
            throw new Exception('Unable to instantiate wishlist - user is not logged in.');
        }

        $this->wishlist = Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Get current wishlist instance.
     *
     * @return \Modules\Product\Repositories\WishlistRepository
     */
    public function getInstance(): WishlistRepository
    {
        return $this;
    }

    /**
     * Add item to the wishlist.
     *
     * @param \Modules\Product\Models\Product $product
     * @param \Modules\Product\Models\ParameterAttribute|int|null $attribute
     * @return bool
     */
    public function insertItem(Product $product, $attribute = null): bool
    {
        if (!$product->hasCountableParameters()) {
            $this->wishlist->items()->firstOrCreate([
                'product_id' => $product->id,
            ]);

            return true;
        }

        if (!$attribute instanceof ParameterAttribute) {
            $attribute = $product->enabledAttributes()->find($attribute);
        }

        if (!$attribute || !$attribute->exists) {
            return false;
        }

        $this->wishlist->items()->firstOrCreate([
            'product_id'   => $product->id,
            'attribute_id' => $attribute->id,
        ]);

        return true;
    }

    /**
     * Remove item from the wishlist.
     *
     * @param \Modules\Product\Models\Wishlist\WishlistItem $wishlistItem
     * @return bool
     */
    public function removeItem(WishlistItem $wishlistItem): bool
    {
        try {
            return $wishlistItem->delete();
        } catch (Exception $e) {
            logger()->critical('[API\WishlistController] Unable to remove wishlist item - ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Remove all items in wishlist.
     *
     * @return mixed
     */
    public function removeAllItems()
    {
        return $this->wishlist->items()->delete();
    }

    /**
     * Prepare response for client side.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function response(): JsonResponse
    {
        $items = [];

        foreach ($this->wishlist->items as $item) {
            if (!$item->product || ($item->product->hasCountableParameters() && !$item->attribute)) {
                $item->delete();
                continue;
            }

            $title = $item->product->title;

            // Get quantity.
            if ($item->product->hasCountableParameters()) {
                $enabledAttribute = optional($item->product->enabledAttributes->where('id', $item->attribute_id)->first());
                $quantity = optional($enabledAttribute->pivot)->quantity ?? 0;

                $title .= ' - ' . $item->attribute->name . $item->attribute->parameter->postfix;
            } else {
                $quantity = $item->product->quantity ?? 0;
            }

            // Status text and availability.
            if ($quantity) {
                $statusText = lg('wishlist.available', 'Pieejams');
                $isAvailable = true;
            } else {
                $statusText = lg('wishlist.not_available', 'Nav pieejams');
                $isAvailable = false;
            }

            $items[] = [
                'id'           => $item->id,
                'product_id'   => $item->product_id,
                'attribute_id' => $item->attribute_id,
                'title'        => $title,
                'price'        => $item->product->price,
                'is_available' => $isAvailable,
                'status_text'  => $statusText,
                'created_at'   => $item->created_at->format('d.m.Y H:i:s'),
            ];
        }

        return response()->json($items);
    }
}