<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOrder extends Model
{
    protected $table = 'payment_orders';

    protected static function booted()
    {
        static::addGlobalScope('regular', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where('payment_orders.is_prepaid', 0);
        });

        static::creating(function ($model) {
            if ($model->is_prepaid === null) {
                $model->is_prepaid = 0;
            }
        });
    }

    protected $fillable = [
        'claim_number',
        'account_type',
        'gp_number',
        'amount',
        'deduction',
        'taxes',
        'invoice_count_after_review',
        'amount_after_review',
        'due_date',
        'payer_entity_id',
        'payee_hospital_id',
        'department_id',
        'electronic_invoice_no',
        'invoice_no',
        'notes',
        'attachments',
        'branch',
        'location',
        'beneficiary',
        'user_id',
        'is_prepaid'
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'deduction' => 'decimal:2',
        'taxes' => 'decimal:2',
        'amount_after_review' => 'decimal:2',
        'attachments' => 'array',
    ];

    public function payerEntity(): BelongsTo
    {
        return $this->belongsTo(ClaimEntity::class, 'payer_entity_id');
    }

    public function payeeHospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'payee_hospital_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function claim(): BelongsTo
    {
        // Many payment orders link to the claim via claim_number
        // (Note: some old flows might use electronic_invoice_no instead, but claim_number is standard)
        return $this->belongsTo(Claim::class, 'claim_number', 'claim_number');
    }
}
