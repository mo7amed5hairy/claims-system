<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('draft_closures')) {
            return;
        }

        Schema::create('draft_closures', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->foreignId('user_id')->index();
            $table->string('reason', 16);
            $table->timestamp('closed_at');
            $table->timestamps();

            $table->unique(['user_id', 'uuid']);
        });

        // Backfill from drafts that are no longer pending (if any remain).
        if (Schema::hasTable('drafts')) {
            DB::table('drafts')
                ->where('status', '!=', 'pending')
                ->orderBy('id')
                ->chunk(500, function ($drafts) {
                    $now = now();
                    $rows = [];
                    foreach ($drafts as $draft) {
                        $rows[] = [
                            'uuid' => $draft->uuid,
                            'user_id' => $draft->user_id,
                            'reason' => $draft->status,
                            'closed_at' => $draft->finalized_at ?: ($draft->updated_at ?: $now),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    if ($rows !== []) {
                        DB::table('draft_closures')->insertOrIgnore($rows);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('draft_closures');
    }
};
