<?php

namespace Opcodes\Spike\Asaas\Services\Requests;

use Sammyjo20\Saloon\Constants\Method;
use Sammyjo20\Saloon\Http\SaloonRequest;

class CreatePaymentRequest extends SaloonRequest
{
    protected ?string $method = Method::POST;

    public function __construct(
        protected array $paymentData
    ) {}

    public function defineEndpoint(): string
    {
        return '/payments';
    }

    public function defaultData(): array
    {
        return $this->paymentData;
    }
}