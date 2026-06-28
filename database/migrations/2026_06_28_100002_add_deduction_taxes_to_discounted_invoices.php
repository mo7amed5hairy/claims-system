<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('discounted_invoices', function (Blueprint $table) {
            $table->decimal('deduction_amount', 15, 2)->default(0)->after('discounted_amount');
            $table->decimal('taxes_amount', 15, 2)->default(0)->after('deduction_amount');
        });
    }

    public function down()
    {
        Schema::table('discounted_invoices', function (Blueprint $table) {
            $table->dropColumn(['deduction_amount', 'taxes_amount']);
        });
    }
};
