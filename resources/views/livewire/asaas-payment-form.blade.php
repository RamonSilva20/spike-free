<div>
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ __('Escolha a forma de pagamento') }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
                Selecione como deseja pagar pelos seus itens
            </p>
        </div>

        <div class="px-4 py-5 sm:px-6">
            <div class="space-y-4">
                <!-- Boleto Bancário -->
                <div class="relative">
                    <div class="flex items-center">
                        <input
                            type="radio"
                            id="boleto"
                            wire:model.live="selectedPaymentMethod"
                            value="BOLETO"
                            class="h-4 w-4 text-brand focus:ring-brand border-gray-300"
                            wire:click="selectPaymentMethod('BOLETO')"
                        >
                        <label for="boleto" class="ml-3 flex items-center cursor-pointer">
                            <svg class="h-6 w-6 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Boleto Bancário</div>
                                <div class="text-sm text-gray-500">Pagamento via boleto - prazo de 3 dias úteis</div>
                            </div>
                        </label>
                    </div>

                    @if($selectedPaymentMethod === 'BOLETO')
                    <div class="mt-4 ml-7 p-4 bg-gray-50 rounded-md">
                        <label for="boletoDueDate" class="block text-sm font-medium text-gray-700">
                            Data de vencimento
                        </label>
                        <input
                            type="date"
                            id="boletoDueDate"
                            wire:model="boletoDueDate"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand focus:border-brand sm:text-sm"
                            min="{{ now()->addDays(1)->format('Y-m-d') }}"
                        >
                    </div>
                    @endif
                </div>

                <!-- PIX -->
                <div class="relative">
                    <div class="flex items-center">
                        <input
                            type="radio"
                            id="pix"
                            wire:model.live="selectedPaymentMethod"
                            value="PIX"
                            class="h-4 w-4 text-brand focus:ring-brand border-gray-300"
                            wire:click="selectPaymentMethod('PIX')"
                        >
                        <label for="pix" class="ml-3 flex items-center cursor-pointer">
                            <svg class="h-6 w-6 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900">PIX</div>
                                <div class="text-sm text-gray-500">Pagamento instantâneo via QR Code</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Cartão de Crédito -->
                <div class="relative">
                    <div class="flex items-center">
                        <input
                            type="radio"
                            id="credit_card"
                            wire:model.live="selectedPaymentMethod"
                            value="CREDIT_CARD"
                            class="h-4 w-4 text-brand focus:ring-brand border-gray-300"
                            wire:click="selectPaymentMethod('CREDIT_CARD')"
                        >
                        <label for="credit_card" class="ml-3 flex items-center cursor-pointer">
                            <svg class="h-6 w-6 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-gray-900">Cartão de Crédito</div>
                                <div class="text-sm text-gray-500">Pagamento com cartão de crédito</div>
                            </div>
                        </label>
                    </div>

                    @if($selectedPaymentMethod === 'CREDIT_CARD')
                    <div class="mt-4 ml-7 p-4 bg-gray-50 rounded-md space-y-4">
                        <div>
                            <label for="cardNumber" class="block text-sm font-medium text-gray-700">
                                Número do cartão
                            </label>
                            <input
                                type="text"
                                id="cardNumber"
                                wire:model="cardNumber"
                                placeholder="1234 5678 9012 3456"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand focus:border-brand sm:text-sm"
                            >
                            @error('cardNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="cardExpiryMonth" class="block text-sm font-medium text-gray-700">
                                    Mês
                                </label>
                                <select
                                    id="cardExpiryMonth"
                                    wire:model="cardExpiryMonth"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand focus:border-brand sm:text-sm"
                                >
                                    <option value="">Mês</option>
                                    @for($month = 1; $month <= 12; $month++)
                                        <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                @error('cardExpiryMonth') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="cardExpiryYear" class="block text-sm font-medium text-gray-700">
                                    Ano
                                </label>
                                <select
                                    id="cardExpiryYear"
                                    wire:model="cardExpiryYear"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand focus:border-brand sm:text-sm"
                                >
                                    <option value="">Ano</option>
                                    @for($year = date('Y'); $year <= date('Y') + 10; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                @error('cardExpiryYear') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="cardHolderName" class="block text-sm font-medium text-gray-700">
                                Nome no cartão
                            </label>
                            <input
                                type="text"
                                id="cardHolderName"
                                wire:model="cardHolderName"
                                placeholder="Como está escrito no cartão"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand focus:border-brand sm:text-sm"
                            >
                            @error('cardHolderName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="cardCvv" class="block text-sm font-medium text-gray-700">
                                CVV
                            </label>
                            <input
                                type="text"
                                id="cardCvv"
                                wire:model="cardCvv"
                                placeholder="123"
                                maxlength="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand focus:border-brand sm:text-sm"
                            >
                            @error('cardCvv') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($selectedPaymentMethod)
            <div class="mt-6">
                <button
                    type="button"
                    wire:click="processPayment"
                    class="w-full bg-brand hover:opacity-80 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand"
                >
                    Continuar com o pagamento
                </button>
            </div>
            @endif
        </div>
    </div>
</div>