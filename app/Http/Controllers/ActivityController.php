<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $latestVideo = $user->videos()->latest()->first();

        $resultValue = $latestVideo && $latestVideo->result
            ? $latestVideo->result->result
            : 0;

        $query = Activity::query();
        // Uncomment if needed
        // if ($resultValue == 0) {
        //     $query->where('category', '!=', 'Communication');
        // }

        $activities = $query->with('category')->get();

        return response()->json($activities, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|file|mimes:mp4,mov,avi|max:102400',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('content')) {
            $validated['content'] = $request->file('content')->store('videos', 'public');
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('activities', 'public');
        }

        $activity = Activity::create($validated);

        return response()->json([
            'message' => 'Activity created successfully!',
            'data' => $activity
        ], 201);
    }

    public function show($id)
    {
        $activity = Activity::with('category')->find($id);

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        return response()->json($activity);
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::find($id);

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|file|mimes:mp4,mov,avi|max:102400',
            'category_id' => 'required|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('content')) {
            $validated['content'] = $request->file('content')->store('videos', 'public');
        }

        if ($request->hasFile('photo')) {
            if ($activity->photo && Storage::disk('public')->exists($activity->photo)) {
                Storage::disk('public')->delete($activity->photo);
            }

            $validated['photo'] = $request->file('photo')->store('activities', 'public');
        }

        $activity->update($validated);

        return response()->json([
            'message' => 'Activity updated successfully!',
            'data' => $activity
        ]);
    }

    public function destroy($id)
    {
        $activity = Activity::find($id);

        if (!$activity) {
            return response()->json(['error' => 'Activity not found'], 404);
        }

        if ($activity->photo && Storage::disk('public')->exists($activity->photo)) {
            Storage::disk('public')->delete($activity->photo);
        }

        $activity->delete();

        return response()->json(['message' => 'Activity deleted successfully.']);
    }

    public function categories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
}
