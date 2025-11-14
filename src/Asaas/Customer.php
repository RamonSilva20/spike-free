<?php

namespace Opcodes\Spike\Asaas;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'asaas_customers';

    protected $fillable = [
        'asaas_id',
        'billable_type',
        'billable_id',
        'name',
        'email',
        'phone',
        'mobile_phone',
        'cpf_cnpj',
        'person_type',
        'company_name',
        'city',
        'state',
        'country',
        'postal_code',
        'address',
        'address_number',
        'complement',
        'province',
        'external_reference',
        'disabled',
        'additional_emails',
        'municipal_inscription',
        'state_inscription',
        'observations',
    ];

    protected $casts = [
        'disabled' => 'boolean',
        'additional_emails' => 'array',
    ];

    public function billable()
    {
        return $this->morphTo();
    }
}