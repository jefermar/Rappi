<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function sections()
    {
        return $this->belongsTo(Section::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
