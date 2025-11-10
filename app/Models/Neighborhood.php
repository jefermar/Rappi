<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasSmartScopes;

    public $fillable = ['id','name', 'city_id', 'state_id'];

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
