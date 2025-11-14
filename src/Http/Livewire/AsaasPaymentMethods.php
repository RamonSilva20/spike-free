<?php

namespace Opcodes\Spike\Http\Livewire;

use Livewire\Component;
use Opcodes\Spike\Facades\Spike;

class AsaasPaymentMethods extends Component
{
    public array $paymentMethods = [];
    public bool $paymentMethodsLoaded = false;
    public ?string $defaultPaymentMethod;

    protected $listeners = [
        'paymentMethodAdded' => 'loadPaymentMethods',
    ];

    public function render()
    {
        return view('spike::livewire.asaas-payment-methods');
    }

    public function loadPaymentMethods()
    {
        $billable = Spike::resolve();
        // TODO: Load Asaas payment methods
        // Asaas doesn't have stored payment methods like Stripe
        // This might be for displaying available payment options
        $this->paymentMethods = []; // Asaas payment methods are typically one-time
        $this->paymentMethodsLoaded = true;
    }

    public function setDefaultMethod($paymentMethod)
    {
        // Asaas doesn't have default payment methods in the same way
        // This might not be applicable
        $this->defaultPaymentMethod = $paymentMethod;
    }
}