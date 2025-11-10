<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasSmartScopes;

    public $fillable = ['id','name', 'position_id', 'postal_code_id'];
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
