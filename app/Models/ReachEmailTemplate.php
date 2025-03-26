<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachEmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'reach_email_templates';

    protected $fillable = [
        'template_type',
        'template_subject',
        'template_tags',
        'template_message',
        'template_to_status',
        'template_to_address',
        'template_cc_address',
        'template_bcc_address',
        'template_title',
        'template_title',
        'mailchimp_id',
    ];
}
