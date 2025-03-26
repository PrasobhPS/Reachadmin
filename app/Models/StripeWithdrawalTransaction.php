<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeWithdrawalTransaction extends Model
{
    use HasFactory;

    protected $table = 'stripe_withdrawal_transaction';

    protected $fillable = [
        'member_id',
        'transaction_id',
        'account_id',
        'transfer_amount',
        'transfer_date',
        'currency',
        'status',
        'failed_reason',
        'balance_transaction',
        'withdrawal_type'
    ];

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }
}
