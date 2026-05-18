<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->decimal('deduction', 15, 2)->nullable()->after('amount');
            $table->decimal('taxes', 15, 2)->nullable()->after('deduction');
        });
    }

    public function down(): void
    {
        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropColumn(['deduction', 'taxes']);
        });
    }
};
