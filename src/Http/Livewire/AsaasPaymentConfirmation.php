<?php

namespace Opcodes\Spike\Http\Livewire;

use Livewire\Component;
use Opcodes\Spike\Asaas\Transaction;

class AsaasPaymentConfirmation extends Component
{
    public Transaction $transaction;
    public string $paymentMethod;

    public function mount(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->paymentMethod = $transaction->billing_type;
    }

    public function copyPixPayload()
    {
        // This would trigger a JavaScript copy to clipboard
        $this->dispatch('copy-to-clipboard', $this->transaction->pix_payload);
    }

    public function render()
    {
        return view('spike::livewire.asaas-payment-confirmation');
    }
}