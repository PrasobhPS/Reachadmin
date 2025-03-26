<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachCountry extends Model
{
    use HasFactory;

    protected $table = 'reach_countries';

    public $timestamps = false;

    protected $fillable = [
        'country_iso',
        'country_name',
        'country_iso3',
        'country_numcode',
        'country_phonecode',
        'country_status',
    ];
}
