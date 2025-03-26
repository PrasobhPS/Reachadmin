<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripePaymentTransfer extends Model
{
    use HasFactory;

    protected $table = 'stripe_payment_transfer';

    protected $fillable = [
        'member_id',
        'booking_id',
        'stripe_transaction_id',
        'connected_account_id',
        'transfer_amount',
        'transfer_date',
        'balance_transaction',
        'status',
        'from_currency',
        'to_currency',
        'converted_amount',
        'exchange_rate',
        'withdraw_id'
    ];

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }
}
