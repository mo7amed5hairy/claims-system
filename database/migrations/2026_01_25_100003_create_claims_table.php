<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('month'); // Storing as "YYYY-MM" or "January" depending on logic. Will use "YYYY-MM" effectively or just string.
            $table->foreignId('hospital_id')->constrained();
            $table->foreignId('department_id')->constrained();
            $table->integer('invoice_count');
            $table->date('claim_date');
            $table->decimal('claim_value', 15, 2);
            $table->string('reviewer_name')->nullable();
            $table->decimal('reviewed_value', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable(); // reviewed_value - claim_value
            $table->string('electronic_invoice_no')->nullable();
            $table->foreignId('entity_id')->constrained('claim_entities');
            $table->string('insurance_claim_number')->nullable(); // For Misr Insurance
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
