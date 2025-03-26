<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachPageTitle extends Model
{
    use HasFactory;

    protected $table = 'reach_page_title';

    protected $fillable = [
        'page_id',
        'page_title',
        'page_desc',
        'page_step',
        'page_step_title',
    ];
}
