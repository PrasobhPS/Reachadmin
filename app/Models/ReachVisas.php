<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachVisas extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_name',
        'country_iso',
        'country_status',
    ];
}
