<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReachEventImage extends Model
{
    use HasFactory;

    protected $table = 'reach_event_images';

    protected $fillable = [
        'event_id',
        'event_images',
        'event_images_status'
        // Add other fields that are mass assignable
    ];

    public function event()
    {
        return $this->belongsTo(ReachEvents::class, 'event_id');
    }
}
