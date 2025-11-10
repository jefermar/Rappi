<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalespersonOrder extends Model
{
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function employees()
    {
        return $this->belongsTo(Employee::class);
    }
}
