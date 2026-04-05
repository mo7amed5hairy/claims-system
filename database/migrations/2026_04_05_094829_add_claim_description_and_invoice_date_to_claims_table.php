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
        Schema::table('claims', function (Blueprint $table) {
            // Add new fields for waiting lists insurance flow (reviewer users)
            $table->text('claim_description')->nullable()->after('notes');
            $table->date('electronic_invoice_date')->nullable()->after('claim_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['claim_description', 'electronic_invoice_date']);
        });
    }
};
