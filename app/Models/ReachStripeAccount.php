<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachStripeAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'access_token',
        'scope',
        'refresh_token',
        'token_type',
        'stripe_user_id',
        'livemode',
        'status',
        'created_by',
    ];
}
