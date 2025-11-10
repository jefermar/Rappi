<?php

namespace App\Models;

use App\Traits\HasSmartScopes;
use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    use HasSmartScopes;
    public $fillable = ['id','name','neighborhood_id'];
    public function neighborhoods()
    {
        return $this->belongsTo(Neighborhood::class);
    }
}
