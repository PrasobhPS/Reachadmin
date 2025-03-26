<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachEmployeeMedia extends Model
{
    use HasFactory;

    protected $table = 'reach_employee_media';

    protected $fillable = [
        'employee_id',
        'media_file',
    ];
}
