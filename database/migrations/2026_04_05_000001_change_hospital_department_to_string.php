<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['hospital_id']);
            $table->dropForeign(['department_id']);
            
            // Change hospital_id and department_id to string to support waiting lists
            $table->string('hospital_id')->change();
            $table->string('department_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            // Revert back to unsigned big integers
            $table->unsignedBigInteger('hospital_id')->change();
            $table->unsignedBigInteger('department_id')->change();
            
            // Re-add foreign keys
            $table->foreign('hospital_id')->references('id')->on('hospitals');
            $table->foreign('department_id')->references('id')->on('departments');
        });
    }
};
