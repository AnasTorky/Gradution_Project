<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; 

class VideoController extends Controller
{
    public function create()
    {
        return view('video.upload'); 
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov', 
        ]);

        // Handle the file upload
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');

            // Create a new Video record in the database
            $video = Video::create([
                'video' => $videoPath,  
                'user_id' => Auth::id(), 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Now, send the video to the Flask API for prediction
            $response = Http::attach(
                'video',
                file_get_contents(storage_path('app/public/' . $videoPath)),
                basename($videoPath)
            )->post('http://127.0.0.1:5000/predict'); // Flask server URL

            // Check if the request was successful
            if ($response->successful()) {
                // Retrieve the prediction from the response
                $prediction = $response->json()['prediction'];

                // Save the result in the database
                Result::create([
                    'video_id' => $video->id,  
                    'result' => $prediction, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Redirect to the result page with the prediction
                return view('video.result', [
                    'result' => $prediction,
                    'video' => $video
                ]);
            } else {
                // Handle the error if the prediction fails
                return redirect()->route('video.upload')->with('error', 'Failed to get prediction from AI model.');
            }
        }

        // Redirect back with error if no file was uploaded
        return redirect()->route('video.upload')->with('error', 'No video file uploaded.');
    }
}


