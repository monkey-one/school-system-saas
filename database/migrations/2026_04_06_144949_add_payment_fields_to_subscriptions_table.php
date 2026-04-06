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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->decimal('payment_amount', 12, 2)->nullable()->after('payment_reference');
            $table->string('payment_token')->nullable()->after('payment_amount');
            $table->string('billing_cycle')->default('monthly')->after('payment_token');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_amount', 'payment_token', 'billing_cycle']);
        });
    }
};
