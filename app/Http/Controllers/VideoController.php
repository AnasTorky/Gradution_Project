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
                $response = Http::attach(
                    'video',
                    file_get_contents(storage_path('app/public/' . $videoPath)),
                    basename($videoPath)
                )->post('http://127.0.0.1:5000/full-analysis'); // Flask server URL// Flask API URL

                // Check if the request was successful
                if ($response->successful()) {
                    // Retrieve all data from the API response
                    $apiResponse = $response->json();

                    // Get the main prediction results
                    $result_prediction = $apiResponse['stage1_prediction'] ?? null;
                    $class_prediction = $apiResponse['stage2_behavior'] ?? null;
                    $severity = $apiResponse['severity'] ?? 'none';

                    // Save all results in the database
                    Result::create([
                        'video_id' => $video->id,
                        'result' => $result_prediction,
                        'class' => $class_prediction,
                        'severity' => $severity,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Prepare additional data for view
                    $analysisData = [
                        'face_analysis' => $apiResponse['face_analysis'] ?? null,
                        'movement_analysis' => $apiResponse['movement_analysis'] ?? null,
                        'combined_score' => $apiResponse['combined_score'] ?? null
                    ];

                    // Redirect to the result page with all data
                    return view('video.result', [
                        'result' => $result_prediction,
                        'class' => $class_prediction,
                        'severity' => $severity,
                        'analysis_data' => $analysisData,
                        'video' => $video
                    ]);
                }
                else {
                    // Handle the error if the prediction fails
                    return redirect()->route('video.upload')->with('error', 'Failed to get prediction from AI model. Status: ' . $response->status());
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
