<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];
    public $timestamps = false;

    public function villages()
    {
        return $this->hasMany(Village::class);
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
