<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'reach_notifications';
    protected $fillable = [
        'message',
        'employee_id',
        'job_id',
        'notified_by',
        'notified_to',
        'is_read',
        'url_keyword',
        'notification_type'
    ];
}
