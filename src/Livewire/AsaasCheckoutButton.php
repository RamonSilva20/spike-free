<?php

namespace Opcodes\Spike\Livewire;

use Livewire\Component;
use Opcodes\Spike\Cart;
use Opcodes\Spike\Facades\Spike;

class AsaasCheckoutButton extends Component
{
    public function checkout()
    {
        // TODO: Implement checkout process
        // This would redirect to Asaas or process payment
    }

    public function render()
    {
        return view('spike::livewire.asaas-checkout-button', [
            'asaasCheckout' => $this->getAsaasCheckoutObject(),
        ]);
    }

    protected function cart(): Cart
    {
        return Cart::forBillable(Spike::resolve());
    }

    private function getAsaasCheckoutObject()
    {
        if (! Spike::paymentProvider()->isAsaas()) {
            return null;
        }

        // TODO: Return Asaas checkout object
        return null;
    }
}