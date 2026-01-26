<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_orders', function (Blueprint $table) {
            $table->id();
            $table->string('account_type'); // Bank, Check, etc.
            $table->string('gp_number')->nullable(); // GP, Check No.
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->foreignId('payer_entity_id')->constrained('claim_entities');
            $table->foreignId('payee_hospital_id')->constrained('hospitals');
            $table->string('electronic_invoice_no');
            $table->string('invoice_no');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_orders');
    }
};
