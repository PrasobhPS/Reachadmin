<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmNotification extends Model
{
    use HasFactory;

    protected $table = 'fcm_notification_access_tokens';

    protected $fillable = [
        'member_id',
        'device_type',
        'token',
        'is_login',
    ];

}
