<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOrder extends Model
{
    protected $table = 'payment_orders';

    protected $fillable = [
        'account_type',
        'gp_number',
        'amount',
        'due_date',
        'payer_entity_id',
        'payee_hospital_id',
        'electronic_invoice_no',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function payerEntity(): BelongsTo
    {
        return $this->belongsTo(ClaimEntity::class, 'payer_entity_id');
    }

    public function payeeHospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'payee_hospital_id');
    }
}
