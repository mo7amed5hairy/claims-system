<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returned_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('month');
            $table->foreignId('hospital_id')->constrained();
            $table->foreignId('department_id')->constrained();
            $table->date('return_date');
            $table->decimal('value', 15, 2); // This is the original claim value
            $table->integer('returned_invoice_count')->default(0);
            $table->decimal('reviewed_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2)->default(0);
            $table->string('electronic_invoice_no');
            $table->foreignId('entity_id')->constrained('claim_entities');
            $table->string('reviewer_name')->nullable();
            $table->json('attachments')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returned_invoices');
    }
};
