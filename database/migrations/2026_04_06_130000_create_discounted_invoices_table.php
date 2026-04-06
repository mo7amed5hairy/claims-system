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
        Schema::create('discounted_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('claims')->onDelete('cascade');
            $table->string('claim_number');
            $table->integer('original_invoice_count');
            $table->integer('discounted_invoice_count');
            $table->decimal('original_amount', 15, 2);
            $table->decimal('discounted_amount', 15, 2);
            $table->decimal('unpaid_amount', 15, 2); // المبلغ الغير مسدد
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounted_invoices');
    }
};
