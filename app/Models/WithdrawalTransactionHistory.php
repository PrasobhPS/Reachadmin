<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalTransactionHistory extends Model
{
    use HasFactory;
    protected $table = 'withdrawal_transaction_history';

    // Define the fillable fields
    protected $fillable = [
        'member_id',
        'withdrawal_id',
        'payment_id',
        'connected_account_id',
        'from_currency',
        'to_currency',
        'exchange_rate',
        'converted_amount',
        'transfer_date',
    ];
}
