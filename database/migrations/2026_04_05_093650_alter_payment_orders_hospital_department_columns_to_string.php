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
            // Change columns to string to support waiting list values
            $table->string('payee_hospital_id', 255)->change();
            $table->string('department_id', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            // Revert back to integer (unsigned big integer typically)
            $table->unsignedBigInteger('payee_hospital_id')->change();
            $table->unsignedBigInteger('department_id')->change();
        });
    }
};
