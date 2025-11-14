<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asaas_customers', function (Blueprint $table) {
            $table->id();
            $table->string('asaas_id')->unique();
            $table->foreignId('billable_id');
            $table->string('billable_type');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('cpf_cnpj')->nullable();
            $table->string('person_type')->nullable(); // FISICA or JURIDICA
            $table->string('company_name')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('complement')->nullable();
            $table->string('province')->nullable();
            $table->string('external_reference')->nullable();
            $table->boolean('disabled')->default(false);
            $table->json('additional_emails')->nullable();
            $table->string('municipal_inscription')->nullable();
            $table->string('state_inscription')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['billable_id', 'billable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asaas_customers');
    }
};