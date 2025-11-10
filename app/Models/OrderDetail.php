<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function warrantiesByOrder()
    {
        return $this->hasMany(Warranty::class, 'OrderDetail_order_id', 'id');
    }
    public function warrantiesByProduct()
    {
        return $this->hasMany(Warranty::class, 'OrderDetail_product_id', 'id');
    }
}
