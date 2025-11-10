<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    public function salespersonOrders()
    {
        return $this->hasMany(SalespersonOrder::class);
    }
}
