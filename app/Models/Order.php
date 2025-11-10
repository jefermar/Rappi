<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name','salesperson_order_id', 'shipping_method_id', 'user_id'];
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
