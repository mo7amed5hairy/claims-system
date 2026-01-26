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
        'status'
    ];

    protected $casts = [
        'claim_date' => 'date',
        'claim_value' => 'decimal:2',
        'reviewed_value' => 'decimal:2',
        'difference' => 'decimal:2',
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
}
