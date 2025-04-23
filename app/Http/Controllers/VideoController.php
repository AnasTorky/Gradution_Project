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

        // Check if a video file has been uploaded
        if ($request->hasFile('video')) {
            // Handle the file upload
            $videoPath = $request->file('video')->store('videos', 'public');

            // Create a new Video record in the database
            $video = Video::create([
                'video' => $videoPath,  
                'user_id' => Auth::id(), 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send the video to the Flask API for prediction
            try {
                $response = Http::withoutVerifying()->attach(
                    'video',  // Field name as expected by Flask
                    file_get_contents(storage_path('app/public/' . $videoPath)),
                    basename($videoPath)
                )->post('https://web-production-0e42.up.railway.app//predict');  // Flask API URL

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
            } catch (\Exception $e) {
                // Handle connection errors
                return redirect()->route('video.upload')->with('error', 'Error communicating with the Flask API: ' . $e->getMessage());
            }
        }

        // Redirect back with error if no file was uploaded
        return redirect()->route('video.upload')->with('error', 'No video file uploaded.');
    }
}
