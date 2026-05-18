<?php

namespace App\Modules\Claims\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DiscountedInvoice extends Model
{
    use HasFactory;

    protected $table = 'discounted_invoices';

    protected $fillable = [
        'claim_id',
        'claim_number',
        'original_invoice_count',
        'discounted_invoice_count',
        'original_amount',
        'discounted_amount',
        'unpaid_amount',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discounted_amount' => 'decimal:2',
        'unpaid_amount' => 'decimal:2',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentOrder()
    {
        return $this->hasOneThrough(
            PaymentOrder::class,
            Claim::class,
            'id',
            'claim_number',
            'claim_id',
            'claim_number'
        );
    }
}
