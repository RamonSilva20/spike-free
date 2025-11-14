<?php

namespace Opcodes\Spike\Asaas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Opcodes\Spike\Contracts\ProvideHistoryRelatableItemContract;
use Opcodes\Spike\Contracts\SpikeSubscriptionItem;
use Opcodes\Spike\Database\Factories\Asaas\SubscriptionItemFactory;

class SubscriptionItem extends Model implements ProvideHistoryRelatableItemContract, SpikeSubscriptionItem
{
    use HasFactory;

    protected $table = 'asaas_subscription_items';

    protected static function newFactory()
    {
        return SubscriptionItemFactory::new();
    }

    public function getPriceId(): string
    {
        return $this->asaas_price;
    }

    public function provideHistoryId(): string
    {
        return $this->getKey().':'.$this->getPriceId();
    }

    public function provideHistoryType(): string
    {
        return $this->getMorphClass();
    }
}