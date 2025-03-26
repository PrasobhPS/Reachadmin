<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reach_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'payment_id',
        'member_id',
        'connected_member_id',
        'parent_transaction_id',
        'original_amount',
        'reduced_amount',
        'actual_amount',
        'from_currency',
        'to_currency',
        'rate',
        'payment_date',
        'status',
        'type',
        'description',
        'transaction_type'
    ];
    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }
    
    public function connectedMember()
    {
        return $this->belongsTo(ReachMember::class, 'connected_member_id', 'id');
    }
    /**
     * Define a relationship to the member who made the transaction.
     */
    public function getDetails()
    {
        return [
            'transaction_id' => $this->transaction_id,
            'payment_date' =>  date('d-m-Y', strtotime($this->payment_date)),
            'original_amount' => $this->original_amount,
            'converted_original_amount' => $this->convertAmount($this->original_amount, $this->rate),
            'reduced_amount' => $this->reduced_amount,
            'converted_reduced_amount' => $this->convertAmount($this->reduced_amount, $this->rate),
            'actual_amount' => $this->actual_amount,
            'converted_actual_amount' => $this->convertAmount($this->actual_amount, $this->rate),
            'type' => $this->type,
            'description' => $this->description,
            'transaction_type' => $this->transaction_type,
            'rate' => $this->rate,
            'status' => $this->status,
            'member_name' => $this->member ? $this->member->members_fname . ' ' . $this->member->members_lname : 'N/A',
            'connected_member_name' => $this->connectedMember ? $this->connectedMember->members_fname . ' ' . $this->connectedMember->members_lname : 'N/A',
            'from_currency' => $this->from_currency,
            'to_currency' => $this->to_currency,
        ];
    }

    // Helper method to convert amounts using the rate
    private function convertAmount($amount, $rate)
    {
        return $amount && $rate ? round($amount * $rate, 2) : null;
    }
   
}
