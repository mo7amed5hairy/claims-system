<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->boolean('is_prepaid')->default(0)->after('id');
        });

        Schema::table('payment_orders', function (Blueprint $table) {
            $table->boolean('is_prepaid')->default(0)->after('id');
        });

        Schema::table('discounted_invoices', function (Blueprint $table) {
            $table->boolean('is_prepaid')->default(0)->after('id');
        });
    }

    public function down()
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn('is_prepaid');
        });

        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropColumn('is_prepaid');
        });

        Schema::table('discounted_invoices', function (Blueprint $table) {
            $table->dropColumn('is_prepaid');
        });
    }
};
