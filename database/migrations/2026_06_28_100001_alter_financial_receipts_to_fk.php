<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Add FK columns as nullable
        Schema::table('financial_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('payee_hospital_id')->nullable()->after('payer_entity_name');
            $table->unsignedBigInteger('payee_department_id')->nullable()->after('payee_hospital_id');
        });

        // Step 2: Migrate existing data (resolve names to IDs)
        DB::statement("
            UPDATE financial_receipts r
            JOIN hospitals h ON h.name = r.payee_hospital_name
            SET r.payee_hospital_id = h.id
        ");
        DB::statement("
            UPDATE financial_receipts r
            JOIN departments d ON d.name = r.payee_department_name
            SET r.payee_department_id = d.id
        ");

        // Step 3: Make payee_hospital_id NOT NULL, drop old text columns, add FK constraints
        Schema::table('financial_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('payee_hospital_id')->nullable(false)->change();
            $table->foreign('payee_hospital_id')->references('id')->on('hospitals');
            $table->foreign('payee_department_id')->references('id')->on('departments');
            $table->dropColumn(['payee_hospital_name', 'payee_department_name']);
        });
    }

    public function down()
    {
        Schema::table('financial_receipts', function (Blueprint $table) {
            $table->string('payee_hospital_name')->nullable()->after('payer_entity_name');
            $table->string('payee_department_name')->nullable()->after('payee_hospital_name');
        });

        // Restore names from IDs
        DB::statement("
            UPDATE financial_receipts r
            JOIN hospitals h ON h.id = r.payee_hospital_id
            SET r.payee_hospital_name = h.name
        ");
        DB::statement("
            UPDATE financial_receipts r
            JOIN departments d ON d.id = r.payee_department_id
            SET r.payee_department_name = d.name
        ");

        Schema::table('financial_receipts', function (Blueprint $table) {
            $table->dropForeign(['payee_hospital_id']);
            $table->dropForeign(['payee_department_id']);
            $table->dropColumn(['payee_hospital_id', 'payee_department_id']);
        });
    }
};
