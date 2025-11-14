<?php

namespace Opcodes\Spike\Asaas;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'asaas_transactions';

    protected $fillable = [
        'asaas_id',
        'billable_type',
        'billable_id',
        'customer_id',
        'subscription_id',
        'installment_number',
        'payment_date',
        'due_date',
        'value',
        'net_value',
        'billing_type',
        'status',
        'description',
        'external_reference',
        'original_value',
        'interest_value',
        'fine_value',
        'discount_value',
        'effective_date',
        'end_to_end_identifier',
        'invoice_url',
        'bank_slip_url',
        'invoice_number',
        'pix_qr_code',
        'pix_payload',
        'pix_expiration_date',
        'deleted',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
        'effective_date' => 'datetime',
        'pix_expiration_date' => 'datetime',
        'value' => 'decimal:2',
        'net_value' => 'decimal:2',
        'original_value' => 'decimal:2',
        'interest_value' => 'decimal:2',
        'fine_value' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'deleted' => 'boolean',
    ];

    public function billable()
    {
        return $this->morphTo();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'asaas_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'asaas_id');
    }
}