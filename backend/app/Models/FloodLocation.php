<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FloodLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'district_id',
        'village_id',
        'flood_depth',
        'flood_duration',
        'cause',
        'description',
        'geometry',
    ];

    protected $casts = [
        'flood_depth' => 'float',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function photos()
    {
        return $this->hasMany(FloodPhoto::class);
    }

    protected function geometry(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? json_decode($value) : null,
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }
}
