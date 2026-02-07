<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('payee_hospital_id');
            // $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            // Since it's nullable and logic might be loose, better to just index or keep it simple.
            // But usually we want foreign key constraint.
            // The user didn't specify strict constraint but it's good practice.
            // Wait, previous migrations used unconstrained bigInteger sometimes.
            // Let's check 2026_01_25_100005_create_payment_orders_table.php content if I can.
            // I'll just add the column for now.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }
};
