<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['payee_hospital_id']);
            
            // Change payee_hospital_id to string to support waiting lists
            $table->string('payee_hospital_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            // Revert back to unsigned big integer
            $table->unsignedBigInteger('payee_hospital_id')->change();
            
            // Re-add foreign key
            $table->foreign('payee_hospital_id')->references('id')->on('hospitals');
        });
    }
};
