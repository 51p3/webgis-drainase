<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DrainagePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'drainage_id',
        'photo_path',
        'caption',
        'photo_date',
    ];

    protected $casts = [
        'photo_date' => 'datetime',
    ];

    public function drainage()
    {
        return $this->belongsTo(Drainage::class);
    }
}
