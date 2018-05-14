<?php

namespace Modules\Product\PassThroughs\CartItem;

use Modules\Product\Models\Cart\CartItem;
use Modules\Product\PassThroughs\PassThrough;

class Format extends PassThrough
{
    /**
     * CartItem model.
     *
     * @var \Modules\Product\Models\Cart\CartItem
     */
    protected $cartItem;

    /**
     * Format constructor.
     *
     * @param \Modules\Product\Models\Cart\CartItem $cartItem
     */
    public function __construct(CartItem $cartItem)
    {
        $this->cartItem = $cartItem;
    }

    /**
     * Map cart item for client cart.
     *
     * @return array
     */
    public function formatForClientCart(): array
    {
        $product = $this->cartItem->product;
        $category = $product->categories->first();
        $url = 'javascript:;';

        if ($category) {
            $url = '/shop/' . $category->slug . '/' . $product->slug;
        }

        if ($this->cartItem->attribute_id) {
            $postfix = null;

            if ($this->cartItem->attribute->parameter->display_as_range) {
                $postfix = $this->cartItem->attribute->parameter->postfix;
            }

            $this->cartItem->product->title .= ' - ' . $this->cartItem->attribute->name . ($postfix);
        }

        return [
            'id'       => $this->cartItem->id,
            'url'      => $url,
            'img'      => $this->cartItem->product->getCoverImage('thumb'),
            'name'     => $this->cartItem->product->title,
            'quantity' => $this->cartItem->quantity,
            'category' => $category->name ?? '',

            'has_discount'           => $this->cartItem->product->priceModel->hasDiscount(),
            'price_with_discount'    => $this->cartItem->product->priceModel->with_vat_with_discount,
            'price_without_discount' => $this->cartItem->product->priceModel->with_vat_without_discount,

            'available_quantity' => $this->cartItem->availableQuantity,
        ];
    }
}