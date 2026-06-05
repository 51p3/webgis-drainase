<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Village extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'district_id'];
    public $timestamps = false;

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function drainages()
    {
        return $this->hasMany(Drainage::class);
    }

    public function floodLocations()
    {
        return $this->hasMany(FloodLocation::class);
    }
}
