<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FloodPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'flood_location_id',
        'photo_path',
        'caption',
        'photo_date',
    ];

    protected $casts = [
        'photo_date' => 'datetime',
    ];

    public function floodLocation()
    {
        return $this->belongsTo(FloodLocation::class);
    }
}
