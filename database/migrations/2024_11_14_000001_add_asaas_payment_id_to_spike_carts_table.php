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
        Schema::table('spike_carts', function (Blueprint $table) {
            $table->string('asaas_payment_id')->nullable()->after('stripe_checkout_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spike_carts', function (Blueprint $table) {
            $table->dropColumn('asaas_payment_id');
        });
    }
};