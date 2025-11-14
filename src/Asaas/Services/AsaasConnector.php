<?php

namespace Opcodes\Spike\Asaas\Services;

use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\Plugins\AcceptsJson;

class AsaasConnector extends SaloonConnector
{
    use AcceptsJson;

    public function __construct(
        protected string $apiKey,
        protected string $environment = 'sandbox'
    ) {}

    public function defineBaseUrl(): string
    {
        return $this->environment === 'production'
            ? 'https://www.asaas.com/api/v3'
            : 'https://sandbox.asaas.com/api/v3';
    }

    public function defaultHeaders(): array
    {
        return [
            'access_token' => $this->apiKey,
        ];
    }

    public function defaultConfig(): array
    {
        return [
            'timeout' => 30,
        ];
    }
}