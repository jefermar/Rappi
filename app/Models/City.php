<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasSmartScopes;

    protected $fillable = ['id','name', 'postalcode_id'];
    
    public function postalcode()
    {
        return $this->belongsTo(PostalCode::class);
    }
    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }
}
