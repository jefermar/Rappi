<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
