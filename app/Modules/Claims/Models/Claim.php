<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasMediaUpload;
use App\Modules\Claims\Traits\ScopedByOwner;

class Claim extends Model
{
    use HasMediaUpload;
    use ScopedByOwner;
    protected $table = 'claims';

    protected static function booted()
    {
        static::addGlobalScope('regular', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where('claims.is_prepaid', 0);
        });

        static::creating(function ($model) {
            if ($model->is_prepaid === null) {
                $model->is_prepaid = 0;
            }
        });
    }

    protected $fillable = [
        'claim_number', // New required field
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
        'delivery_attachments',
        'claim_description',
        'electronic_invoice_date',
        'is_prepaid',
    ];

    protected $casts = [
        'claim_date' => 'date',
        'claim_value' => 'decimal:2',
        'reviewed_value' => 'decimal:2',
        'difference' => 'decimal:2',
        'attachments' => 'array',
        'delivery_attachments' => 'array',
        'delivery_date' => 'date',
        'electronic_invoice_date' => 'date',
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
        // For waiting lists, use claim_number; for regular flows, use electronic_invoice_no
        return $this->hasMany(PaymentOrder::class, 'claim_number', 'claim_number');
    }

    public function paymentsByElectronicInvoice()
    {
        // Keep the old relationship for backward compatibility
        return $this->hasMany(PaymentOrder::class, 'electronic_invoice_no', 'electronic_invoice_no');
    }

    public function allPayments()
    {
        // Find payment orders matching by EITHER claim_number OR electronic_invoice_no,
        // since some payment orders only have one of them populated
        $modelClass = $this instanceof PrepaidClaim
            ? PrepaidPaymentOrder::class
            : PaymentOrder::class;

        return $modelClass::query()
            ->where(function ($query) {
                if (!empty($this->claim_number)) {
                    $query->orWhere('claim_number', $this->claim_number);
                }
                if (!empty($this->electronic_invoice_no)) {
                    $query->orWhere('electronic_invoice_no', $this->electronic_invoice_no);
                }
            })
            ->orderBy('due_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getPaymentStatusAttribute()
    {
        $hasClaimNumber = !empty($this->claim_number);
        $hasElectronicInvoice = !empty($this->electronic_invoice_no);

        if (!$hasClaimNumber && !$hasElectronicInvoice) {
            return 'unpaid';
        }

        // Use a single query with OR to avoid double-counting
        $modelClass = $this instanceof PrepaidClaim
            ? PrepaidPaymentOrder::class
            : PaymentOrder::class;

        $paidAmount = (float) $modelClass::query()
            ->where(function ($query) use ($hasClaimNumber, $hasElectronicInvoice) {
                if ($hasClaimNumber) {
                    $query->orWhere('claim_number', $this->claim_number);
                }
                if ($hasElectronicInvoice) {
                    $query->orWhere('electronic_invoice_no', $this->electronic_invoice_no);
                }
            })
            ->sum('amount');

        if ($paidAmount >= $this->claim_value && $this->claim_value > 0) {
            return 'paid';
        } elseif ($paidAmount > 0) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }
}
