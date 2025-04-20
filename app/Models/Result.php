<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['result','video_id'];
    public function video()
{
    return $this->belongsTo(Video::class);
}
}
