<?php

namespace Opcodes\Spike\Asaas;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Log;
use Opcodes\Spike\Asaas\Services\AsaasService;
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
        $billable = $this->getBillable();
        $asaasService = AsaasService::make();

        // Calculate total amount
        $total = $cart->items->sum(function ($item) {
            return $item->product()->price_in_cents * $item->quantity;
        });

        // Prepare payment data
        $paymentData = [
            'customer' => $this->getOrCreateAsaasCustomer($billable),
            'billingType' => 'UNDEFINED', // Let user choose
            'value' => $total / 100, // Convert cents to reais
            'dueDate' => now()->addDays(3)->format('Y-m-d'),
            'description' => 'Compra - ' . $cart->id,
            'externalReference' => 'cart_' . $cart->id,
            'installmentCount' => 1,
            'installmentValue' => $total / 100,
        ];

        try {
            $response = $asaasService->createPayment($paymentData);

            // Store Asaas payment ID in cart
            $cart->update([
                'asaas_payment_id' => $response['id'],
            ]);

            // Create transaction record
            Transaction::create([
                'billable_type' => $billable->getMorphClass(),
                'billable_id' => $billable->getKey(),
                'asaas_id' => $response['id'],
                'value' => $total,
                'status' => $response['status'],
                'billing_type' => $response['billingType'],
                'description' => $response['description'],
                'external_reference' => $response['externalReference'],
                'due_date' => $response['dueDate'],
                'invoice_url' => $response['invoiceUrl'] ?? null,
                'bank_slip_url' => $response['bankSlipUrl'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Asaas payment creation failed', [
                'cart_id' => $cart->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
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

    protected function getOrCreateAsaasCustomer($billable): string
    {
        // Check if customer already exists
        $customer = Customer::where('billable_type', $billable->getMorphClass())
            ->where('billable_id', $billable->getKey())
            ->first();

        if ($customer) {
            return $customer->asaas_id;
        }

        // Create customer in Asaas
        $asaasService = AsaasService::make();
        $customerData = [
            'name' => $billable->name ?? $billable->spikeEmail(),
            'email' => $billable->spikeEmail(),
            'phone' => $billable->phone ?? null,
            'mobilePhone' => $billable->mobile_phone ?? null,
            'cpfCnpj' => $billable->cpf_cnpj ?? null,
            'externalReference' => $billable->getMorphClass() . '_' . $billable->getKey(),
        ];

        try {
            $response = $asaasService->createCustomer($customerData);

            // Store customer locally
            Customer::create([
                'asaas_id' => $response['id'],
                'billable_type' => $billable->getMorphClass(),
                'billable_id' => $billable->getKey(),
                'name' => $response['name'],
                'email' => $response['email'],
                'phone' => $response['phone'] ?? null,
                'mobile_phone' => $response['mobilePhone'] ?? null,
                'cpf_cnpj' => $response['cpfCnpj'] ?? null,
                'external_reference' => $response['externalReference'] ?? null,
            ]);

            return $response['id'];
        } catch (\Exception $e) {
            Log::error('Asaas customer creation failed', [
                'billable_id' => $billable->getKey(),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}