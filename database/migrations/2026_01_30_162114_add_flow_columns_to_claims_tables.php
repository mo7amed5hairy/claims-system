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
        // Claims Table
        if (Schema::hasTable('claims')) {
            Schema::table('claims', function (Blueprint $table) {
                if (!Schema::hasColumn('claims', 'branch')) {
                    $table->string('branch')->nullable()->after('entity_id')->comment('الفرع / قائمة الانتظار');
                }
                if (!Schema::hasColumn('claims', 'location')) {
                    $table->string('location')->nullable()->after('branch')->comment('المحافظة / الموقع');
                }
                if (!Schema::hasColumn('claims', 'beneficiary')) {
                    $table->string('beneficiary')->nullable()->after('location')->comment('القانون / فئة المنتفعين');
                }
            });
        }

        // Returned Invoices Table
        if (Schema::hasTable('returned_invoices')) {
            Schema::table('returned_invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('returned_invoices', 'branch')) {
                    $table->string('branch')->nullable()->after('entity_id')->comment('الفرع / قائمة الانتظار');
                }
                if (!Schema::hasColumn('returned_invoices', 'location')) {
                    $table->string('location')->nullable()->after('branch')->comment('المحافظة / الموقع');
                }
                if (!Schema::hasColumn('returned_invoices', 'beneficiary')) {
                    $table->string('beneficiary')->nullable()->after('location')->comment('القانون / فئة المنتفعين');
                }
            });
        }

        // Payment Orders Table
        if (Schema::hasTable('payment_orders')) {
            Schema::table('payment_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_orders', 'branch')) {
                    // Uses payer_entity_id instead of entity_id
                    $afterColumn = Schema::hasColumn('payment_orders', 'payer_entity_id') ? 'payer_entity_id' : 'id';
                    $table->string('branch')->nullable()->after($afterColumn)->comment('الفرع / قائمة الانتظار');
                }
                if (!Schema::hasColumn('payment_orders', 'location')) {
                    $table->string('location')->nullable()->after('branch')->comment('المحافظة / الموقع');
                }
                if (!Schema::hasColumn('payment_orders', 'beneficiary')) {
                    $table->string('beneficiary')->nullable()->after('location')->comment('القانون / فئة المنتفعين');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['claims', 'returned_invoices', 'payment_orders'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['branch', 'location', 'beneficiary']);
                });
            }
        }
    }
};
