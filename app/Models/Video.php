<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Video extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function store(Request $request)
{
    // Validate and handle the video upload as before...

    // After successful upload, redirect back to home
    return redirect()->route('home')->with('success', 'Video uploaded successfully!');
}
public function user()
{
    return $this->belongsTo(User::class);
}

public function result()
{
    return $this->hasOne(Result::class);
}

}



