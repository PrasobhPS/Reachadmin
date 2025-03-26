<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatReportMembers extends Model
{
    use HasFactory;

    protected $table = 'chat_report_members';

    protected $fillable = [
        'reported_member_id',
        'reported_by_member_id',
        'report_reason',
    ];
}
