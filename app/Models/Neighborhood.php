<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function street(){
        return $this->hasMany(Street::class);
    }
}
