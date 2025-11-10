<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }
    public function countries()
    {
        return $this->belongsTo(Country::class);
    }
}
