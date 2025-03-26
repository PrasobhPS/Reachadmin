<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachEmployeeSearch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_employee_search_parameters';

    protected $fillable = [
        'search_parameter_name',
        'search_value',
        'employee_id',
    ];
}
