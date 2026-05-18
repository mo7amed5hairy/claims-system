<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOrder extends Model
{
    protected $table = 'payment_orders';

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
        'user_id'
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
}
