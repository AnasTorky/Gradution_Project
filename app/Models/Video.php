<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class Video extends Model
{
    use HasFactory;
    protected $fillable = ['video', 'user_id'];
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



