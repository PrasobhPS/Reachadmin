<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripePaymentTransaction extends Model
{
    use HasFactory;

    protected $table = 'stripe_payment_transaction';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'member_id',
        'payment_to',
        'stripe_payment_intend_id',
        'stripe_subscription_id',
        'stripe_charge_id',
        'amount_paid',
        'payment_date',
        'currency',
        'charge_description',
        'last_4',
        'status',
        'balance_transaction',
        'booking_id',
        'refund_status',
        'payment_type',
        'discount_amount',
        'discount_type',
        'original_amount_paid',
        'parent_currency',
        'refund_amount',
        'specialist_amount'
    ];

    public function specialist()
    {
        return $this->belongsTo(ReachMember::class, 'payment_to', 'id');
    }

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }
    public function paymentToMember()
{
    return $this->belongsTo(ReachMember::class, 'payment_to');
}
}
