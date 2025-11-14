<div>
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="bg-white px-4 py-5 border-b border-gray-200 sm:px-6">
            <div class="flex items-center">
                @if($paymentMethod === 'BOLETO')
                    <svg class="h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                @elseif($paymentMethod === 'PIX')
                    <svg class="h-8 w-8 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                @else
                    <svg class="h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif

                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        @if($paymentMethod === 'BOLETO')
                            Boleto Gerado com Sucesso
                        @elseif($paymentMethod === 'PIX')
                            PIX Gerado com Sucesso
                        @else
                            Pagamento Processado
                        @endif
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        @if($paymentMethod === 'BOLETO')
                            Seu boleto foi gerado e está pronto para pagamento
                        @elseif($paymentMethod === 'PIX')
                            Use o QR Code ou código PIX para fazer o pagamento
                        @else
                            Seu pagamento foi processado com sucesso
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="px-4 py-5 sm:px-6">
            @if($paymentMethod === 'BOLETO' && $transaction->bank_slip_url)
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Imprimir Boleto
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Clique no link abaixo para visualizar e imprimir seu boleto:</p>
                                <a href="{{ $transaction->bank_slip_url }}" target="_blank" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Visualizar Boleto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Vencimento: {{ $transaction->due_date?->format('d/m/Y') }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>O boleto deve ser pago até a data de vencimento para evitar juros e multas.</p>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($paymentMethod === 'PIX')
                <div class="space-y-4">
                    @if($transaction->pix_qr_code)
                        <div class="bg-green-50 border border-green-200 rounded-md p-4">
                            <div class="text-center">
                                <h3 class="text-sm font-medium text-green-800 mb-2">
                                    QR Code PIX
                                </h3>
                                <img src="data:image/png;base64,{{ $transaction->pix_qr_code }}" alt="QR Code PIX" class="mx-auto max-w-xs">
                                <p class="mt-2 text-xs text-green-600">
                                    Escaneie o QR Code com o app do seu banco
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($transaction->pix_payload)
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                        <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Código PIX (Copia e Cola)
                                    </h3>
                                    <div class="mt-2">
                                        <textarea readonly rows="3" class="block w-full border-gray-300 rounded-md shadow-sm bg-white text-xs font-mono" id="pix-payload">{{ $transaction->pix_payload }}</textarea>
                                        <button
                                            type="button"
                                            onclick="navigator.clipboard.writeText(document.getElementById('pix-payload').value)"
                                            class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Copiar Código
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($transaction->pix_expiration_date)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Válido até: {{ $transaction->pix_expiration_date->format('d/m/Y H:i') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>O código PIX expira na data acima. Após isso, será necessário gerar um novo pagamento.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            @else
                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Pagamento Aprovado
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Seu pagamento foi processado com sucesso. Você receberá um e-mail de confirmação em breve.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <strong>Valor:</strong> R$ {{ number_format($transaction->value, 2, ',', '.') }}
                </div>
                <div class="text-sm text-gray-600">
                    <strong>ID da Transação:</strong> {{ $transaction->asaas_id }}
                </div>
            </div>
        </div>
    </div>
</div>