<?php

namespace Opcodes\Spike\Asaas\Services\Requests;

use Sammyjo20\Saloon\Constants\Method;
use Sammyjo20\Saloon\Http\SaloonRequest;

class CreateCustomerRequest extends SaloonRequest
{
    protected ?string $method = Method::POST;

    public function __construct(
        protected array $customerData
    ) {}

    public function defineEndpoint(): string
    {
        return '/customers';
    }

    public function defaultData(): array
    {
        return $this->customerData;
    }
}