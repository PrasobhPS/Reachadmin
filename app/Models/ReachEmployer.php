<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Sanctum\HasApiTokens;

class ReachEmployer extends Authenticatable
{
    use HasFactory, HasApiTokens;
    use SoftDeletes;

    protected $table = 'reach_employers';
    protected $primaryKey = 'employer_id';

    protected $fillable = [
        'member_id',
        'employer_company_name',
        'employer_email',
        'employer_phone',
        'employer_country',
        'employer_vessel_name',
        'employer_profile_picture',
        'employer_status',
        'employer_phone_code',
        'is_deleted',
        'deleted_at',
    ];

    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }

}
