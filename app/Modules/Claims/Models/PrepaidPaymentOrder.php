<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Builder;

class PrepaidPaymentOrder extends PaymentOrder
{
    protected $table = 'payment_orders';

    protected static function booted()
    {
        // Override the 'regular' scope inherited from PaymentOrder
        static::addGlobalScope('prepaid', function (Builder $builder) {
            $builder->where('payment_orders.is_prepaid', 1);
        });

        static::creating(function ($model) {
            $model->is_prepaid = 1;
        });
    }

    public function claim(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PrepaidClaim::class, 'claim_number', 'claim_number');
    }

    // Override accessors to use PrepaidClaim instead of Claim
    public function getBranchAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        // Try to get from linked prepaid claim
        $claim = $this->claim;
        return $claim ? $claim->branch : null;
    }

    public function getLocationAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        // Try to get from linked prepaid claim
        $claim = $this->claim;
        return $claim ? $claim->location : null;
    }

    public function getBeneficiaryAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        // Try to get from linked prepaid claim
        $claim = $this->claim;
        return $claim ? $claim->beneficiary : null;
    }
}
