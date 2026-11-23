<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Draft extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_FINALIZED = 'finalized';
    public const STATUS_DISCARDED = 'discarded';

    /**
     * Whitelist of draft types mapped to Arabic labels.
     */
    public const TYPES = [
        'claim_create' => 'مطالبة',
        'claim_edit' => 'تعديل مطالبة',
        'prepaid_claim_create' => 'مطالبة دفع مقدم',
        'prepaid_claim_edit' => 'تعديل مطالبة دفع مقدم',
        'payment_create' => 'أمر دفع',
        'payment_edit' => 'تعديل أمر دفع',
        'prepaid_payment_create' => 'أمر دفع مقدم',
        'prepaid_payment_edit' => 'تعديل أمر دفع مقدم',
    ];

    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'title',
        'payload',
        'revision',
        'status',
        'finalized_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'finalized_at' => 'datetime',
        'revision' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Derive a short title from payload for display.
     */
    public static function titleFromPayload(string $type, array $payload): ?string
    {
        $parts = [];
        $claimNo = $payload['claim_number'] ?? $payload['electronic_invoice_no'] ?? null;
        if ($claimNo) $parts[] = 'رقم: ' . $claimNo;
        $name = $payload['beneficiary_name'] ?? $payload['company_name'] ?? null;
        if ($name) $parts[] = $name;
        $val = $payload['claim_value'] ?? $payload['amount'] ?? null;
        if ($val) $parts[] = 'القيمة: ' . $val;
        $t = implode(' - ', array_filter($parts));
        return $t !== '' ? mb_substr($t, 0, 190) : null;
    }
}
