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
        Schema::create('asaas_subscription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asaas_subscription_id');
            $table->string('asaas_price');
            $table->string('status');
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['asaas_subscription_id', 'asaas_price'], 'asi_asaas_subscription_id_price_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asaas_subscription_items');
    }
};