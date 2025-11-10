<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class User extends model
{
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
