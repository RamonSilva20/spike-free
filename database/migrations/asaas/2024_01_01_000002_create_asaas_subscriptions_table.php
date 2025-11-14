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
        Schema::create('asaas_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billable_id');
            $table->string('billable_type');
            $table->string('type');
            $table->string('asaas_id')->unique();
            $table->string('asaas_status');
            $table->string('asaas_price');
            $table->integer('quantity')->default(1);
            $table->decimal('value', 10, 2);
            $table->timestamp('next_due_date')->nullable();
            $table->string('cycle')->nullable(); // WEEKLY, MONTHLY, QUARTERLY, SEMIANNUALLY, YEARLY
            $table->text('description')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('promotion_code_id')->nullable();
            $table->timestamps();

            $table->index(['billable_id', 'billable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asaas_subscriptions');
    }
};