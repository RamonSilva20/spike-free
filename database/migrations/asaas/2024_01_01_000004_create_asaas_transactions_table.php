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
        Schema::create('asaas_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billable_id');
            $table->string('billable_type');
            $table->string('asaas_id')->unique();
            $table->string('customer_id')->nullable()->index();
            $table->string('subscription_id')->nullable()->index();
            $table->integer('installment_number')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->decimal('value', 10, 2);
            $table->decimal('net_value', 10, 2)->nullable();
            $table->string('billing_type')->nullable(); // BOLETO, CREDIT_CARD, PIX, etc.
            $table->string('status'); // PENDING, RECEIVED, CONFIRMED, etc.
            $table->text('description')->nullable();
            $table->string('external_reference')->nullable();
            $table->decimal('original_value', 10, 2)->nullable();
            $table->decimal('interest_value', 10, 2)->nullable();
            $table->decimal('fine_value', 10, 2)->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->timestamp('effective_date')->nullable();
            $table->string('end_to_end_identifier')->nullable();
            $table->string('invoice_url')->nullable();
            $table->string('bank_slip_url')->nullable();
            $table->string('invoice_number')->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamps();

            $table->index(['billable_id', 'billable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asaas_transactions');
    }
};