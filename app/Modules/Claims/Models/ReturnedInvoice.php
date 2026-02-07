<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasMediaUpload;

class ReturnedInvoice extends Model
{
    use HasMediaUpload;

    protected $table = 'returned_invoices';

    protected $fillable = [
        'month',
        'hospital_id',
        'department_id',
        'return_date',
        'value',
        'returned_invoice_count',
        'reviewed_value',
        'discount_amount',
        'tax_amount',
        'final_amount',
        'electronic_invoice_no',
        'entity_id',
        'reviewer_name',
        'attachments',
        'reason',
        'branch',
        'location',
        'beneficiary',
        'user_id'
    ];

    protected $casts = [
        'return_date' => 'date',
        'value' => 'decimal:2',
        'reviewed_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'attachments' => 'array',
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
}
