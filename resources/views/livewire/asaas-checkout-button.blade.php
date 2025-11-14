@if($showPaymentForm)
    <livewire:spike::asaas-payment-form :cart="$cart" @payment-method-selected="paymentMethodSelected" />
@else
    <button
        type="button"
        class="flex items-center bg-brand hover:opacity-80 text-white rounded-md shadow px-3 py-2 text-sm"
        wire:click="checkout"
    >
        <svg class="size-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.1 5H19M7 13l-1.1 5M7 13l1.1-5m8.9 5L17 8m-8.9 5H7m8.9 0h2.1M9 21h6m-3-3v3" />
        </svg>
        {{ __('spike::translations.checkout') }}
    </button>
@endif