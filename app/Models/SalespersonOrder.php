<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class SalespersonOrder extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name','postalcode_id', 'employee_id'];
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
