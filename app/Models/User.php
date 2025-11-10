<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;


class User extends model
{
    use HasSmartScopes;
    public $fillable = ['id','name','email','postal_code_id'];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function postalcode()
    {
        return $this->belongsTo(PostalCode::class);
    }
}
