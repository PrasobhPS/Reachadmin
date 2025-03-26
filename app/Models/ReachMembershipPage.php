<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachMembershipPage extends Model
{
    use HasFactory;
    protected $table = 'reachMembershipPage';
    protected $fillable = [
        'page_header',
        'page_description',
        'page_slug',
        'membership_title',
        'membership_description',
        'membership_button',
        'status',
        'images',
    ];
}
