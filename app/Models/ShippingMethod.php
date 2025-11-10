<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name'];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
