<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialistRating extends Model
{
    use HasFactory;
    protected $table = 'reach_specialist_ratings';
    protected $fillable = [
        'specialist_id',
        'member_id',
        'rating',
        'review',
    ];
    // Define relationship with the Specialist model
    public function specialist()
    {
        return $this->belongsTo(ReachMember::class);
    }
    public function member()
    {
        return $this->belongsTo(ReachMember::class);
    }


    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }
}
