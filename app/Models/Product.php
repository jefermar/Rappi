<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name', 'section_id'];

    public function sections()
    {
        return $this->belongsTo(Section::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
