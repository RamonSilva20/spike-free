<?php

namespace Opcodes\Spike\Asaas;

use Opcodes\Spike\Cart;
use Opcodes\Spike\CartItem;

class CartCheckout
{
    public function __construct(
        protected Cart $cart
    )
    {
    }

    public function resetCheckout(): void
    {
        $this->cart->update([
            'asaas_checkout_id' => null,
        ]);
    }

    /**
     * Process payment for the cart using Asaas
     */
    public function processPayment(): bool
    {
        $billable = $this->cart->billable;

        // TODO: Implement payment processing via Asaas API
        // This would create a payment in Asaas for the cart items

        return false; // Placeholder
    }

    protected function prepareItems(): array
    {
        return $this->cart->items->map(function (CartItem $item) {
            $product = $item->product();

            return [
                'description' => $product->name,
                'quantity' => $item->quantity,
                'value' => $product->price_in_cents,
            ];
        })->toArray();
    }
}