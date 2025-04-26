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
        set_time_limit(300);
        // Validate the incoming request
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov|max:102400', // Added max size (100MB)
        ]);

        // Check if a video file has been uploaded
        if (!$request->hasFile('video') || !$request->file('video')->isValid()) {
            Log::error('No valid video file uploaded');
            return redirect()->route('video.upload')->with('error', 'No valid video file uploaded.');
        }

        // Store the video
        $videoPath = $request->file('video')->store('videos', 'public');
        $fullPath = storage_path('app/public/' . $videoPath);
        Log::info('Video path: ' . $fullPath);
        Log::info('File exists: ' . (file_exists($fullPath) ? 'Yes' : 'No'));
        Log::info('File readable: ' . (is_readable($fullPath) ? 'Yes' : 'No'));

        // Verify file is readable
        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Log::error('Video file not found or not readable: ' . $fullPath);
            return redirect()->route('video.upload')->with('error', 'Video file could not be processed.');
        }

        // Create video record
        $video = Video::create([
            'video' => $videoPath,
            'user_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info('Video record created: ID ' . $video->id);

        // Send to Flask API
        try {
            $response = Http::timeout(300)->attach(
                'video',
                file_get_contents($fullPath),
                basename($videoPath)
            )->post('http://127.0.0.1:5000/full-analysis');

            Log::info('Flask API status: ' . $response->status());
            Log::info('Flask API response: ' . json_encode($response->json(), JSON_PRETTY_PRINT));

            if ($response->successful()) {
                $apiResponse = $response->json();

                // Validate response structure
                if (!is_array($apiResponse) || !isset($apiResponse['status'])) {
                    Log::error('Invalid Flask API response structure: ' . json_encode($apiResponse));
                    return redirect()->route('video.upload')->with('error', 'Invalid response from AI model.');
                }

                // Check if Flask returned an error
                if ($apiResponse['status'] !== 'success') {
                    Log::error('Flask API error response: ' . json_encode($apiResponse));
                    return redirect()->route('video.upload')->with('error', 'AI model error: ' . ($apiResponse['error'] ?? 'Unknown error'));
                }

                // Extract prediction results
                $result_prediction = $apiResponse['stage1_prediction'] ?? null;
                $class_prediction = $apiResponse['stage2_behavior'] ?? null;
                $severity = $apiResponse['severity'] ?? 'none';
                $face_analysis = $apiResponse['face_analysis'] ?? null;
                $movement_analysis = $apiResponse['movement_analysis'] ?? null;
                $combined_score =  $apiResponse['combined_score'] ?? null;

                // Save to database
                $result = Result::create([
                    'video_id' => $video->id,
                    'result' => $result_prediction,
                    'class' => $class_prediction,
                    'severity' => $severity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info('Result saved to database: ID ' . $result->id);



                // Return result view
                 return view('video.result',
                 compact("result_prediction","class_prediction","severity","face_analysis","movement_analysis","combined_score")
                 //[
                //     'result' => $result_prediction,
                //     'class' => $class_prediction,
                //     'severity' => $severity,
                //     'video' => $video,]
                );
            } else {
                Log::error('Flask API failed: Status ' . $response->status() . ', Body: ' . $response->body());
                return redirect()->route('video.upload')->with('error', 'Failed to get prediction. Status: ' . $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection error: ' . $e->getMessage());
            return redirect()->route('video.upload')->with('error', 'Cannot connect to Flask API: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General error: ' . $e->getMessage());
            return redirect()->route('video.upload')->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
