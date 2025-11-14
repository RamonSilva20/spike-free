<?php

namespace Opcodes\Spike\Asaas\Listeners;

use Illuminate\Support\Facades\Log;
use Opcodes\Spike\Actions\Subscriptions\ProvideSubscriptionPlanMonthlyProvides;
use Opcodes\Spike\Contracts\SpikeBillable;
use Opcodes\Spike\CreditTransaction;
use Opcodes\Spike\Events\SubscriptionActivated;
use Opcodes\Spike\Facades\Credits;
use Opcodes\Spike\Facades\Spike;

class AsaasWebhookListener
{
    /**
     * Handle received Asaas webhooks.
     */
    public function handle(array $payload): void
    {
        $eventType = $payload['event'] ?? null;

        switch ($eventType) {
            case 'PAYMENT_RECEIVED':
                $this->handlePaymentReceived($payload);
                break;

            case 'PAYMENT_OVERDUE':
                $this->handlePaymentOverdue($payload);
                break;

            case 'SUBSCRIPTION_CREATED':
                $this->handleSubscriptionCreated($payload);
                break;

            case 'SUBSCRIPTION_UPDATED':
                $this->handleSubscriptionUpdated($payload);
                break;

            default:
                Log::info('Unhandled Asaas webhook event', ['event' => $eventType, 'payload' => $payload]);
                break;
        }
    }

    protected function handlePaymentReceived(array $payload): void
    {
        // TODO: Handle payment received webhook
        Log::info('Asaas payment received', $payload);
    }

    protected function handlePaymentOverdue(array $payload): void
    {
        // TODO: Handle payment overdue webhook
        Log::info('Asaas payment overdue', $payload);
    }

    protected function handleSubscriptionCreated(array $payload): void
    {
        // TODO: Handle subscription created webhook
        Log::info('Asaas subscription created', $payload);
    }

    protected function handleSubscriptionUpdated(array $payload): void
    {
        // TODO: Handle subscription updated webhook
        Log::info('Asaas subscription updated', $payload);
    }
}