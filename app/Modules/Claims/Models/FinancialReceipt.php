<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReceipt extends Model
{
    protected $table = 'financial_receipts';

    protected $fillable = [
        'payer_entity_name',
        'payee_hospital_id',
        'payee_department_id',
        'amount',
        'remaining_amount',
        'receipt_date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'receipt_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'payee_hospital_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'payee_department_id');
    }
}
