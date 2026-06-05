<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Drainage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'district_id',
        'village_id',
        'length',
        'width',
        'height',
        'type',
        'condition',
        'description',
        'geometry',
    ];

    protected $casts = [
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
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
        return $this->hasMany(DrainagePhoto::class);
    }

    protected function geometry(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? json_decode($value) : null,
            set: fn ($value) => is_array($value) ? json_encode($value) : $value,
        );
    }
}
