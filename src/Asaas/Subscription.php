<?php

namespace Opcodes\Spike\Asaas;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Opcodes\Spike\Contracts\SpikeSubscription;
use Opcodes\Spike\Database\Factories\Asaas\SubscriptionFactory;
use Opcodes\Spike\Facades\PaymentGateway as PaymentGatewayFacade;

class Subscription extends Model implements SpikeSubscription
{
    use HasFactory;

    protected $table = 'asaas_subscriptions';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'renews_at' => 'datetime',
    ];

    protected static function newFactory()
    {
        return SubscriptionFactory::new();
    }

    public function getBillable()
    {
        return $this->owner;
    }

    public function getForeignKey()
    {
        return 'asaas_subscription_id';
    }

    public function getPriceId(): string
    {
        return $this->asaas_price;
    }

    public function isPastDue(): bool
    {
        return $this->asaas_status === 'OVERDUE';
    }

    public function getPromotionCodeId(): ?string
    {
        return $this->promotion_code_id;
    }

    /**
     * Get the subscription items related to the subscription.
     *
     * @return HasMany|Collection|SubscriptionItem[]
     */
    public function items()
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    public function hasPaymentCard(): bool
    {
        return ! empty($this->asaas_id);
    }

    public function hasPromotionCode(): bool
    {
        return !is_null($this->promotionCode());
    }

    public function promotionCode()
    {
        // TODO: Implement promotion code for Asaas
        return null;
    }

    public function renewalDate(): ?CarbonInterface
    {
        if ($this->onGracePeriod()) {
            return $this->ends_at;
        }

        if ($this->hasPaymentCard()) {
            // TODO: Get from Asaas API
            return Carbon::createFromTimestamp(time() + 30 * 24 * 60 * 60); // Placeholder
        }

        return $this->renews_at ?? $this->created_at->copy()->addMonthNoOverflow();
    }

    public function cancel(bool $cancelNow = false)
    {
        if ($cancelNow) {
            return $this->cancelNow();
        }

        if ($this->hasPaymentCard()) {
            // TODO: Cancel via Asaas API
            return $this;
        }

        if ($this->onTrial()) {
            $this->ends_at = $this->trial_ends_at;
        } else {
            $this->ends_at = $this->renewalDate();
        }

        $this->save();

        return $this;
    }

    public function cancelNow()
    {
        if ($this->hasPaymentCard()) {
            // TODO: Cancel now via Asaas API
            return $this;
        }

        $this->markAsCanceled();

        return $this;
    }

    public function cancelNowAndInvoice()
    {
        if ($this->hasPaymentCard()) {
            // TODO: Cancel now and invoice via Asaas API
            return $this;
        }

        $this->markAsCanceled();

        return $this;
    }

    public function stopCancelation()
    {
        return $this->resume();
    }

    public function resume($resumeAt = null)
    {
        if ($this->hasPaymentCard()) {
            // TODO: Resume via Asaas API
            return $this;
        }

        $this->fill([
            'asaas_status' => 'ACTIVE',
            'ends_at' => null,
        ])->save();

        return $this;
    }

    public function active()
    {
        return $this->asaas_status === 'ACTIVE' && is_null($this->ends_at);
    }

    public function hasPriceId(string $priceId): bool
    {
        return $this->asaas_price === $priceId
            || $this->items->where('asaas_price', $priceId)->isNotEmpty();
    }

    protected function markAsCanceled()
    {
        $this->fill([
            'asaas_status' => 'INACTIVE',
            'ends_at' => $this->onTrial() ? $this->trial_ends_at : now(),
        ])->save();
    }

    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}