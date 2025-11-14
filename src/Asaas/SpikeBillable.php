<?php

namespace Opcodes\Spike\Asaas;

use Illuminate\Database\Eloquent\Collection;
use Opcodes\Spike\SpikeInvoice;
use Opcodes\Spike\Traits\ManagesCredits;
use Opcodes\Spike\Traits\ManagesPromotionCode;
use Opcodes\Spike\Traits\ManagesPurchases;
use Opcodes\Spike\Traits\ManagesSubscriptions;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property-read Collection|Subscription[] $subscriptions
 *
 * @method Subscription subscription(string $name = 'default')
 */
trait SpikeBillable
{
    use ManagesCredits;
    use ManagesPurchases;
    use ManagesSubscriptions;
    use ManagesPromotionCode;

    public function spikeCacheIdentifier(): string
    {
        return $this->getMorphClass() . ':' . $this->getKey();
    }

    public function spikeEmail()
    {
        // TODO: Implement spikeEmail for Asaas
        return $this->email ?? null;
    }

    public function spikeInvoices()
    {
        // TODO: Implement spikeInvoices using Asaas API
        return collect(); // Return empty collection for now
    }
}