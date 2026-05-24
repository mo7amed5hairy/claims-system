<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Builder;

class PrepaidDiscountedInvoice extends DiscountedInvoice
{
    protected $table = 'discounted_invoices';

    protected static function booted()
    {
        // Override the 'regular' scope inherited from DiscountedInvoice
        static::addGlobalScope('prepaid', function (Builder $builder) {
            $builder->where('discounted_invoices.is_prepaid', 1);
        });

        static::creating(function ($model) {
            $model->is_prepaid = 1;
        });
    }

    public function claim()
    {
        return $this->belongsTo(PrepaidClaim::class, 'claim_id');
    }

    public function paymentOrder()
    {
        return $this->hasOneThrough(
            PrepaidPaymentOrder::class,
            PrepaidClaim::class,
            'id',
            'claim_number',
            'claim_id',
            'claim_number'
        );
    }
}
