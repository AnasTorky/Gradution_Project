<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    
    public function video()
{
    return $this->belongsTo(Video::class);
}
}
