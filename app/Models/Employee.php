<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function postalCode()
    {
        return $this->belongsTo(PostalCode::class);
    }
    public function positions()
    {
        return $this->belongsTo(Position::class);
    }
    public function salespersonOrder()
    {
        return $this->hasMany(SalespersonOrder::class);
    }
}
