<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
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
