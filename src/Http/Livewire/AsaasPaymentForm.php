<?php

namespace Opcodes\Spike\Http\Livewire;

use Livewire\Component;
use Opcodes\Spike\Cart;
use Opcodes\Spike\Facades\Spike;

class AsaasPaymentForm extends Component
{
    public Cart $cart;
    public string $selectedPaymentMethod = '';
    public array $paymentData = [];

    // Credit card fields
    public string $cardNumber = '';
    public string $cardHolderName = '';
    public string $cardExpiryMonth = '';
    public string $cardExpiryYear = '';
    public string $cardCvv = '';

    // Boleto fields
    public string $boletoDueDate = '';

    protected $rules = [
        'selectedPaymentMethod' => 'required|in:BOLETO,PIX,CREDIT_CARD',
        'cardNumber' => 'required_if:selectedPaymentMethod,CREDIT_CARD',
        'cardHolderName' => 'required_if:selectedPaymentMethod,CREDIT_CARD',
        'cardExpiryMonth' => 'required_if:selectedPaymentMethod,CREDIT_CARD',
        'cardExpiryYear' => 'required_if:selectedPaymentMethod,CREDIT_CARD',
        'cardCvv' => 'required_if:selectedPaymentMethod,CREDIT_CARD',
    ];

    public function mount(Cart $cart)
    {
        $this->cart = $cart;
        $this->boletoDueDate = now()->addDays(3)->format('Y-m-d');
    }

    public function selectPaymentMethod($method)
    {
        $this->selectedPaymentMethod = $method;
        $this->resetValidation();
    }

    public function processPayment()
    {
        $this->validate();

        // Prepare payment data based on selected method
        $this->paymentData = [
            'billingType' => $this->selectedPaymentMethod,
        ];

        if ($this->selectedPaymentMethod === 'CREDIT_CARD') {
            $this->paymentData = array_merge($this->paymentData, [
                'creditCard' => [
                    'holderName' => $this->cardHolderName,
                    'number' => $this->cardNumber,
                    'expiryMonth' => $this->cardExpiryMonth,
                    'expiryYear' => $this->cardExpiryYear,
                    'ccv' => $this->cardCvv,
                ],
                'creditCardHolderInfo' => [
                    'name' => $this->cardHolderName,
                    'email' => Spike::resolve()->email,
                    'cpfCnpj' => Spike::resolve()->cpf_cnpj ?? '',
                    'postalCode' => Spike::resolve()->postal_code ?? '',
                    'addressNumber' => Spike::resolve()->address_number ?? '',
                    'phone' => Spike::resolve()->phone ?? '',
                ],
            ]);
        } elseif ($this->selectedPaymentMethod === 'BOLETO') {
            $this->paymentData['dueDate'] = $this->boletoDueDate;
        }

        // Emit event to parent component
        $this->emitUp('paymentMethodSelected', $this->paymentData);
    }

    public function render()
    {
        return view('spike::livewire.asaas-payment-form');
    }
}