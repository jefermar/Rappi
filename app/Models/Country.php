<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasSmartScopes;

    protected $fillable = ['name', 'postal_code_id'];
    
    public function state(){
        return $this->hasMany(State::class);
    }
}
