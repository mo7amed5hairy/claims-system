<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable(); // e.g. 'company', 'ministry'
            $table->json('metadata')->nullable(); // For dynamic configuration
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_entities');
    }
};
