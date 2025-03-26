<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistCallRate extends Model
{
    use HasFactory;
    protected $table = 'reach_specialist_call_rates';
    protected $fillable = ['specialist_id', 'rate'];
    public function member()
    {
        return $this->belongsTo(ReachMember::class, 'member_id', 'id');
    }
}
