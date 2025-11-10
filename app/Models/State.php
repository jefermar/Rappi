<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name','country_id'];
    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }
    public function countries()
    {
        return $this->belongsTo(Country::class);
    }
}
