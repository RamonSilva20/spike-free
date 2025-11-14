<?php

namespace Opcodes\Spike\Asaas;

use Opcodes\Spike\Cart;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;

class PaymentGatewayFake extends PaymentGateway
{
    protected array $purchasedProducts = [];
    protected array $paidCarts = [];
    protected ?CarbonInterface $renewalDate = null;
    protected bool $hasIncompletePayment = false;

    public function payForCart(Cart $cart, array $paymentData = []): bool
    {
        foreach ($cart->items as $item) {
            if (!isset($this->purchasedProducts[$item->product_id])) {
                $this->purchasedProducts[$item->product_id] = 0;
            }

            $this->purchasedProducts[$item->product_id] += $item->quantity;
        }

        $this->paidCarts[] = $cart->id;

        return true;
    }

    public function createSubscription(\Opcodes\Spike\SubscriptionPlan $plan, bool $requirePaymentCard = true): Subscription
    {
        /** @var Subscription $subscription */
        $subscription = Subscription::factory()->create([
            'billable_id' => $this->getBillable()->getKey(),
            'billable_type' => $this->getBillable()->getMorphClass(),
            'asaas_price' => $plan->payment_provider_price_id,
            'quantity' => 1,
        ]);

        SubscriptionItem::factory()->create([
            'asaas_subscription_id' => $subscription->id,
            'asaas_price' => $plan->payment_provider_price_id,
            'quantity' => 1,
        ]);

        return $subscription;
    }

    public function cancelSubscription(): ?Subscription
    {
        $subscription = $this->getSubscription();
        $subscription->update([
            'ends_at' => $subscription->created_at->copy()->addMonthNoOverflow()
        ]);

        return $subscription;
    }

    public function cancelSubscriptionNow(): ?Subscription
    {
        $subscription = $this->getSubscription();
        $subscription->update([
            'asaas_status' => 'INACTIVE',
            'ends_at' => now(),
        ]);

        return $subscription;
    }

    public function resumeSubscription(): ?Subscription
    {
        $subscription = $this->getSubscription();
        $subscription->update(['ends_at' => null]);

        return $subscription;
    }

    public function switchSubscription(\Opcodes\Spike\SubscriptionPlan $plan, bool $requirePaymentCard = true): Subscription
    {
        $subscription = $this->getSubscription()->fresh();

        $subscription->update([
            'asaas_status' => 'ACTIVE',
            'asaas_price' => $plan->payment_provider_price_id,
            'quantity' => 1,
        ]);

        $subscription->items()->create([
            'asaas_price' => $plan->payment_provider_price_id,
            'quantity' => 1,
        ]);

        // Delete items that aren't attached to the subscription anymore...
        $subscription->items()->where('asaas_price', '!=', $plan->payment_provider_price_id)->delete();

        $subscription->unsetRelation('items');

        return $subscription;
    }

    public function hasIncompleteSubscriptionPayment(): bool
    {
        return $this->hasIncompletePayment;
    }

    public function setHasIncompletePayment(bool $hasIncomplete): void
    {
        $this->hasIncompletePayment = $hasIncomplete;
    }

    public function assertProductPurchased($product, $quantity = 1)
    {
        $product = $product instanceof \Opcodes\Spike\Product ? $product->id : (string) $product;

        \PHPUnit\Framework\Assert::assertTrue(
            isset($this->purchasedProducts[$product]),
            'The product was not purchased.'
        );

        \PHPUnit\Framework\Assert::assertEquals(
            $quantity,
            $this->purchasedProducts[$product],
            'The product was purchased a different amount. '
                .'Expected '.$quantity.', but purchased '.$this->purchasedProducts[$product]
        );
    }

    public function assertCartPaid(Cart $cart)
    {
        \PHPUnit\Framework\Assert::assertTrue(
            $cart->fresh()->paid() && in_array($cart->id, $this->paidCarts),
            'The cart was not paid.',
        );
    }
}