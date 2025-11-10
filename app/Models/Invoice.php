<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasSmartScopes;

    public $fillable = ['id','name', 'payment_method_id','order_id'];

    public function paymentMethod()
    {
        return $this->belongsTo(paymentMethod::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orders()
    {
        return $this->belongsTo(Order::class);
    }
}
