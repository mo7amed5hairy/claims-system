<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Persistent closure guard: prevents a late sync from recreating a draft
 * after it was finalized/discarded/saved.
 */
class DraftClosure
{
    public const TTL_DAYS = 14;

    public static function key(int $userId, string $uuid): string
    {
        return 'drafts:closed:u' . $userId . ':' . $uuid;
    }

    public static function close(int $userId, string $uuid, string $reason = 'closed'): void
    {
        $now = now();

        if (Schema::hasTable('draft_closures')) {
            DB::table('draft_closures')->insertOrIgnore([
                'user_id' => $userId,
                'uuid' => $uuid,
                'reason' => $reason,
                'closed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('draft_closures')
                ->where('user_id', $userId)
                ->where('uuid', $uuid)
                ->update([
                    'reason' => $reason,
                    'closed_at' => $now,
                    'updated_at' => $now,
                ]);
        }

        Cache::put(self::key($userId, $uuid), $reason, now()->addDays(self::TTL_DAYS));
    }

    public static function isClosed(int $userId, string $uuid): bool
    {
        if (Cache::has(self::key($userId, $uuid))) {
            return true;
        }

        if (!Schema::hasTable('draft_closures')) {
            return false;
        }

        $closed = DB::table('draft_closures')
            ->where('user_id', $userId)
            ->where('uuid', $uuid)
            ->exists();

        if ($closed) {
            Cache::put(self::key($userId, $uuid), true, now()->addDays(self::TTL_DAYS));
        }

        return $closed;
    }
}
