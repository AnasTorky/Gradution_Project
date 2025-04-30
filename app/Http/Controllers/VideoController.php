<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'video' => 'required|file|mimes:mp4,avi,mov|max:102400', // Max 100MB
        ]);

        if (!$request->hasFile('video') || !$request->file('video')->isValid()) {
            Log::error('No valid video file uploaded');
            return response()->json(['error' => 'No valid video file uploaded.'], 400);
        }

        // Store the video
        $videoPath = $request->file('video')->store('videos', 'public');
        $fullPath = storage_path('app/public/' . $videoPath);

        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            Log::error('Video file not found or not readable: ' . $fullPath);
            return response()->json(['error' => 'Video file could not be processed.'], 500);
        }


        // Create video record
        $video = Video::create([
            'video' => $videoPath,
            'user_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send video to Flask API
        try {
            $response = Http::timeout(300)->attach(
                'video',
                file_get_contents($fullPath),
                basename($videoPath)
            )->post('http://127.0.0.1:5000/full-analysis');

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (!is_array($apiResponse) || !isset($apiResponse['status'])) {
                    return response()->json(['error' => 'Invalid response from AI model.'], 500);
                }

                if ($apiResponse['status'] !== 'success') {
                    return response()->json(['error' => $apiResponse['error'] ?? 'Unknown error'], 500);
                }

                // Extract and save prediction results
                $result_prediction = $apiResponse['stage1_prediction'] ?? null;
                $class_prediction = $apiResponse['stage2_behavior'] ?? null;
                $severity = $apiResponse['severity'] ?? 'none';
                $face_analysis = $apiResponse['face_analysis'] ?? null;
                $movement_analysis = $apiResponse['movement_analysis'] ?? null;
                $combined_score =  $apiResponse['combined_score'] ?? null;

                $result = Result::create([
                    'video_id' => $video->id,
                    'result' => $result_prediction,
                    'class' => $class_prediction,
                    'severity' => $severity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'message' => 'Analysis successful',
                    'result_prediction' => $result_prediction,
                    'class_prediction' => $class_prediction,
                    'severity' => $severity,
                    'face_analysis' => $face_analysis,
                    'movement_analysis' => $movement_analysis,
                    'combined_score' => $combined_score,
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Failed to get prediction.',
                    'status' => $response->status(),
                    'body' => $response->body()
                ], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json(['error' => 'Connection error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }
}
