<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRequests extends Model
{
    use HasFactory;

    protected $table = 'chat_requests';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
    ];
}
