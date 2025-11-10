<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name', 'order_id', 'product_id'];
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
