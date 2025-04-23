<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $response = Http::timeout(60)->attach(
                'video',
                file_get_contents(storage_path('app/public/' . $videoPath)),
                basename($videoPath)
            )->post('http://127.0.0.1:5000/predict'); // Flask server URL

            // Check if the request was successful
            if ($response->successful()) {
                // Retrieve the prediction from the response
                $result_prediction = $response->json()['stage1_prediction'];
                $class_prediction = $response->json()['stage2_behavior'];

                // Save the result in the database
                Result::create([
                    'video_id' => $video->id,
                    'result' => $result_prediction,
                    'class' => $class_prediction ?? 'none',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                // Redirect to the result page with the prediction
                return view('video.result', [
                    'result' => $result_prediction,
                    'class'=>$class_prediction,
                    'video' => $video
                ]);
            } else {
                Log::error('AI prediction failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return redirect()->route('video.upload')
                    ->with('error', 'Failed to get prediction from AI model. Status: ' . $response->status());
            }

        }

        // Redirect back with error if no file was uploaded
        return redirect()->route('video.upload')->with('error', 'No video file uploaded.');
    }
}
