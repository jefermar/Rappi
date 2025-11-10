<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','code'];
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
