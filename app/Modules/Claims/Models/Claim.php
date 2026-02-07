<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasMediaUpload;

class Claim extends Model
{
    use HasMediaUpload;
    protected $table = 'claims';

    protected $fillable = [
        'invoice_count',
        'month',
        'hospital_id',
        'department_id',
        'claim_date',
        'claim_value',
        'reviewer_name',
        'reviewed_value',
        'difference',
        'electronic_invoice_no',
        'entity_id',
        'insurance_claim_number',
        'notes',
        'attachments',
        'status',
        'branch',
        'location',
        'beneficiary',
        'user_id',
        'delivery_date',
        'delivery_attachments'
    ];

    protected $casts = [
        'claim_date' => 'date',
        'claim_value' => 'decimal:2',
        'reviewed_value' => 'decimal:2',
        'difference' => 'decimal:2',
        'attachments' => 'array',
        'delivery_attachments' => 'array',
        'delivery_date' => 'date',
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(ClaimEntity::class, 'entity_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function payments()
    {
        // Limiting to match electronic_invoice_no which is the common identifier
        return $this->hasMany(PaymentOrder::class, 'electronic_invoice_no', 'electronic_invoice_no');
    }

    public function getPaymentStatusAttribute()
    {
        if (empty($this->electronic_invoice_no)) {
            return 'unpaid';
        }

        $paidAmount = $this->payments->sum('amount');

        if ($paidAmount >= $this->claim_value && $this->claim_value > 0) {
            return 'paid';
        } elseif ($paidAmount > 0) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }
}
