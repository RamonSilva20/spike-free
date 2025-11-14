<div wire:init="loadPaymentMethods">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('spike::translations.payment_methods') }}</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Asaas payment methods and options') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="px-4 py-5 sm:px-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="relative block w-full bg-white rounded-lg p-4 border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Boleto Bancário</p>
                            <p class="text-sm text-gray-500">Pagamento via boleto</p>
                        </div>
                    </div>
                </div>

                <div class="relative block w-full bg-white rounded-lg p-4 border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">PIX</p>
                            <p class="text-sm text-gray-500">Pagamento instantâneo</p>
                        </div>
                    </div>
                </div>

                <div class="relative block w-full bg-white rounded-lg p-4 border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Cartão de Crédito</p>
                            <p class="text-sm text-gray-500">Pagamento com cartão</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$paymentMethodsLoaded)
                <div class="mt-4 flex items-center justify-center text-sm text-gray-600">
                    <x-spike::shared.spinner class="size-4 mr-2" />
                    {{ __('spike::translations.loading') }}
                </div>
            @endif
        </div>
    </div>
</div>