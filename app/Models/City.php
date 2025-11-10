<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasSmartScopes;

    protected $fillable = ['name', 'postal_code_id'];
    
    public function postalCode()
    {
        return $this->belongsTo(PostalCode::class);
    }
    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }
}
