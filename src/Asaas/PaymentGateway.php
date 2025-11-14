<?php

namespace Opcodes\Spike\Asaas;

use Carbon\CarbonInterface;
use Opcodes\Spike\Cart;
use Opcodes\Spike\Contracts\PaymentGatewayContract;
use Opcodes\Spike\Contracts\SpikeSubscription;
use Opcodes\Spike\PaymentProvider;
use Opcodes\Spike\SubscriptionPlan;
use Opcodes\Spike\Traits\ScopedToBillable;

class PaymentGateway implements PaymentGatewayContract
{
    use ScopedToBillable;

    static string $subscriptionName = 'default';

    public function provider(): PaymentProvider
    {
        return PaymentProvider::Asaas;
    }

    public function findBillable($customer_id)
    {
        // TODO: Implement findBillable using Asaas API
        return null;
    }

    public function payForCart(Cart $cart): bool
    {
        // TODO: Implement payForCart using Asaas API
        return false;
    }

    public function invoiceAndPayItems(array $items, array $options = []): bool
    {
        // TODO: Implement invoiceAndPayItems using Asaas API
        return false;
    }

    public function getSubscription(): ?SpikeSubscription
    {
        // TODO: Implement getSubscription
        return null;
    }

    public function getRenewalDate(): ?CarbonInterface
    {
        // TODO: Implement getRenewalDate
        return null;
    }

    public function subscribed(?SubscriptionPlan $plan = null): bool
    {
        // TODO: Implement subscribed
        return false;
    }

    public function createSubscription(SubscriptionPlan $plan, bool $requirePaymentCard = true): SpikeSubscription
    {
        // TODO: Implement createSubscription using Asaas API
        throw new \Exception('Not implemented');
    }

    public function switchSubscription(SubscriptionPlan $plan, bool $requirePaymentCard = true): SpikeSubscription
    {
        // TODO: Implement switchSubscription using Asaas API
        throw new \Exception('Not implemented');
    }

    public function cancelSubscription(): ?SpikeSubscription
    {
        // TODO: Implement cancelSubscription
        return null;
    }

    public function cancelSubscriptionNow(): ?SpikeSubscription
    {
        // TODO: Implement cancelSubscriptionNow
        return null;
    }

    public function resumeSubscription(): ?SpikeSubscription
    {
        // TODO: Implement resumeSubscription
        return null;
    }

    public function hasIncompleteSubscriptionPayment(): bool
    {
        // TODO: Implement hasIncompleteSubscriptionPayment
        return false;
    }

    public function latestSubscriptionPayment(): \Laravel\Paddle\Payment|\Laravel\Cashier\Payment|null
    {
        // TODO: Implement latestSubscriptionPayment
        return null;
    }
}