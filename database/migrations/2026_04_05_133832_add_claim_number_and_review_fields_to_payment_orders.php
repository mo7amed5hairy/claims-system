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
        Schema::table('payment_orders', function (Blueprint $table) {
            // Add claim_number for linking to claims (searchable field)
            $table->string('claim_number', 255)->nullable()->after('id');
            
            // Add review fields for financial users
            $table->integer('invoice_count_after_review')->nullable()->after('amount');
            $table->decimal('amount_after_review', 15, 2)->nullable()->after('invoice_count_after_review');
            
            // Rename amount to collection_amount (we'll handle this in code, not DB)
            // Note: We keep 'amount' column but change label to 'collection_amount' in UI
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropColumn(['claim_number', 'invoice_count_after_review', 'amount_after_review']);
        });
    }
};
