<?php

namespace Opcodes\Spike\Livewire;

use Livewire\Component;
use Opcodes\Spike\Cart;
use Opcodes\Spike\Facades\PaymentGateway;
use Opcodes\Spike\Facades\Spike;

class AsaasCheckoutButton extends Component
{
    public function checkout()
    {
        $cart = $this->cart();

        // Process payment using Asaas
        if (PaymentGateway::payForCart($cart)) {
            // Payment successful, redirect to success page
            return redirect()->route('spike.purchase.validate-cart', ['cart' => $cart->id]);
        } else {
            // Payment failed
            session()->flash('error', 'Erro ao processar pagamento. Tente novamente.');
        }
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