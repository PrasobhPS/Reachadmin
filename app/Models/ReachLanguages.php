<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachLanguages extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_languages';
    protected $primaryKey = 'lang_id';

    protected $fillable = [
        'language_name',
        'language_status',
    ];
}
