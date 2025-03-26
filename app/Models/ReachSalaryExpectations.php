<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReachSalaryExpectations extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reach_salary_expectations';
    protected $primaryKey = 'expectation_id';

    protected $fillable = [
        'expectation_name',
        'expectation_status',
    ];
}
