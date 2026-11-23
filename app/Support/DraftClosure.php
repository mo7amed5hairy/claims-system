<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class DraftClosure
{
    private const PREFIX = 'draft_closed:';
    private const TTL_DAYS = 14;

    public static function close(int $userId, string $uuid): void
    {
        Cache::put(self::key($userId, $uuid), true, now()->addDays(self::TTL_DAYS));
    }

    public static function isClosed(int $userId, string $uuid): bool
    {
        return Cache::has(self::key($userId, $uuid));
    }

    private static function key(int $userId, string $uuid): string
    {
        return self::PREFIX . $userId . ':' . $uuid;
    }
}
