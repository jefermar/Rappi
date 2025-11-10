<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    public function orderDetailByOrder()
    {
        return $this->belongsTo(OrderDetail::class, 'OrderDetail_order_id', 'id');
    }

    public function orderDetailByProduct()
    {
        return $this->belongsTo(OrderDetail::class, 'OrderDetail_product_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'Order_id', 'id');
    }
}
