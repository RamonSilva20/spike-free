<?php

namespace Opcodes\Spike\Asaas\Services;

use Opcodes\Spike\Asaas\Services\Requests\CreateCustomerRequest;
use Opcodes\Spike\Asaas\Services\Requests\CreatePaymentRequest;

class AsaasService
{
    public function __construct(
        protected AsaasConnector $connector
    ) {}

    public static function make(): self
    {
        $apiKey = config('spike.asaas.api_key');
        $environment = config('spike.asaas.environment', 'sandbox');

        $connector = new AsaasConnector($apiKey, $environment);

        return new self($connector);
    }

    public function createPayment(array $data): array
    {
        $request = new CreatePaymentRequest($data);
        $response = $this->connector->send($request);

        return $response->json();
    }

    public function getPayment(string $paymentId): array
    {
        // TODO: Implement get payment request
        return [];
    }

    public function createCustomer(array $data): array
    {
        $request = new CreateCustomerRequest($data);
        $response = $this->connector->send($request);

        return $response->json();
    }

    public function createSubscription(array $data): array
    {
        // TODO: Implement create subscription request
        return [];
    }
}