<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video; 
use App\Models\Result; 
use Illuminate\Support\Facades\Auth; // Add this to use authentication

class VideoController extends Controller
{
    public function create()
    {
        return view('video.upload');  // Returns the video upload form
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov|max:10240', // Max 10MB
        ]);

        // Handle the file upload
        if ($request->hasFile('video')) {
            // Store the uploaded video in the 'videos' folder in the public disk
            $videoPath = $request->file('video')->store('videos', 'public'); 

            // Create a new Video record in the database
            Video::create([
                'video' => $videoPath,  // The path to the video
                'user_id' => Auth::id(), // Get the currently authenticated user's ID
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Redirect to homepage or another page with a success message
        return redirect()->route('home')->with('success', 'Video uploaded successfully!');
    }
}

