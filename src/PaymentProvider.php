<?php

namespace Opcodes\Spike;

enum PaymentProvider: string
{
    case Stripe = 'stripe';
    case Paddle = 'paddle';
    case Asaas = 'asaas';
    case None = 'none';

    public function name(): string
    {
        return match ($this) {
            self::Stripe => 'Stripe',
            self::Paddle => 'Paddle',
            self::Asaas => 'Asaas',
            self::None => 'None',
        };
    }

    public function isValid(): bool
    {
        return in_array($this, [self::Stripe, self::Paddle, self::Asaas]);
    }

    public function isStripe(): bool
    {
        return $this === self::Stripe;
    }

    public function isPaddle(): bool
    {
        return $this === self::Paddle;
    }

    public function isAsaas(): bool
    {
        return $this === self::Asaas;
    }

    public function requiredComposerPackage(): string
    {
        return match ($this) {
            self::Stripe => 'laravel/cashier:"^15.0"',
            self::Paddle => 'laravel/cashier-paddle:"^2.0"',
            self::Asaas => 'saloonphp/saloon:"^3.0"',
            self::None => '',
        };
    }

    public function subscriptionClass(): ?string
    {
        return match ($this) {
            self::Stripe => 'Opcodes\Spike\Stripe\Subscription',
            self::Paddle => 'Opcodes\Spike\Paddle\Subscription',
            self::Asaas => 'Opcodes\Spike\Asaas\Subscription',
            default => null,
        };
    }

    public function subscriptionItemClass(): ?string
    {
        return match ($this) {
            self::Stripe => 'Opcodes\Spike\Stripe\SubscriptionItem',
            self::Paddle => 'Opcodes\Spike\Paddle\SubscriptionItem',
            self::Asaas => 'Opcodes\Spike\Asaas\SubscriptionItem',
            default => null,
        };
    }

    public function supportsCancellationOffers(): bool
    {
        return match ($this) {
            self::Stripe => true,
            default => false,
        };
    }
}
