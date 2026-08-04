<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign('claims_entity_id_foreign');
            $table->foreign('entity_id')->references('id')->on('claim_entities');
        });

        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropForeign('payment_orders_payer_entity_id_foreign');
            $table->foreign('payer_entity_id')->references('id')->on('claim_entities');
        });

        Schema::table('returned_invoices', function (Blueprint $table) {
            $table->dropForeign('returned_invoices_entity_id_foreign');
            $table->foreign('entity_id')->references('id')->on('claim_entities');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropForeign('claims_entity_id_foreign');
            $table->foreign('entity_id')->references('id')->on('claim_entities_old');
        });

        Schema::table('payment_orders', function (Blueprint $table) {
            $table->dropForeign('payment_orders_payer_entity_id_foreign');
            $table->foreign('payer_entity_id')->references('id')->on('claim_entities_old');
        });

        Schema::table('returned_invoices', function (Blueprint $table) {
            $table->dropForeign('returned_invoices_entity_id_foreign');
            $table->foreign('entity_id')->references('id')->on('claim_entities_old');
        });
    }
};
