<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name'];
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
