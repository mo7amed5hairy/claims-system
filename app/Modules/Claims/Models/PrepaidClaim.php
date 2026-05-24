<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Builder;

class PrepaidClaim extends Claim
{
    protected $table = 'claims';

    protected static function booted()
    {
        // Override the 'regular' scope inherited from Claim
        static::addGlobalScope('prepaid', function (Builder $builder) {
            $builder->where('claims.is_prepaid', 1);
        });

        static::creating(function ($model) {
            $model->is_prepaid = 1;
        });
    }

    public function payments()
    {
        return $this->hasMany(PrepaidPaymentOrder::class, 'claim_number', 'claim_number');
    }

    public function paymentsByElectronicInvoice()
    {
        return $this->hasMany(PrepaidPaymentOrder::class, 'electronic_invoice_no', 'electronic_invoice_no');
    }
}
