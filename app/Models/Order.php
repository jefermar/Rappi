<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }
    public function salespersonOrder()
    {
        return $this->belongsTo(SalespersonOrder::class);
    }
    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function warranty()
    {
        return $this->hasMany(Warranty::class);
    }
}
