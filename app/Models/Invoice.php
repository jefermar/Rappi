<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
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
