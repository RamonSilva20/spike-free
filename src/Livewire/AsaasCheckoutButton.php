<?php

namespace Opcodes\Spike\Livewire;

use Livewire\Component;
use Opcodes\Spike\Cart;
use Opcodes\Spike\Facades\PaymentGateway;
use Opcodes\Spike\Facades\Spike;

class AsaasCheckoutButton extends Component
{
    public bool $showPaymentForm = false;
    public array $paymentData = [];

    protected $listeners = ['paymentMethodSelected'];

    public function checkout()
    {
        // Show payment method selection form
        $this->showPaymentForm = true;
    }

    public function paymentMethodSelected($paymentData)
    {
        $this->paymentData = $paymentData;

        $cart = $this->cart();

        // Process payment using Asaas with selected payment method
        if (PaymentGateway::payForCart($cart, $this->paymentData)) {
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
            'cart' => $this->cart(),
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