<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use Illuminate\Http\Request;
use Auth;

class ActivityController extends Controller
{
    public function index()
{
    $user = Auth::user();

    $latestVideo = $user->videos()->latest()->first();

    $resultValue = 0; // Default

    if ($latestVideo && $latestVideo->result) {
        $resultValue = $latestVideo->result->result; // Access result through relationship
    }

    $query = Activity::query();

    if ($resultValue == 0) {
        $query->where('category', '!=', 'communication');
    }

    $activities = $query->get();

    return view('activities.index', compact('activities'));
}    public function create(){
        return view('activities.create');
       

    }

    public function store(Request $request)
{
    // Validation
    $validated = $request->validate([
        'name' => 'required|max:255',
        'category' => 'required|max:255',
        'description' => 'nullable',
        'content' => 'nullable',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Handle file upload 
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('public/activities');
    } else {
        $photoPath = null;
    }

    // Store activity in the database
    Activity::create([
        'name' => $validated['name'],
        'category' => $validated['category'],
        'description' => $validated['description'],
        'content' => $validated['content'],
        'photo' => $photoPath,
    ]);

    return redirect()->route('activities.index')->with('success', 'Activity created successfully!');
}

    
    public function show($id)
    {
        $activity = Activity::findOrFail($id);
        return view('activities.show', compact('activity'));
    }
    public function edit($id)
    {
        $activity = Activity::findOrFail($id);
        return view('activities.edit', compact('activity'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'category' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

       
        $activity = Activity::findOrFail($id);
        $activity->name = $request->name;
        $activity->description = $request->description;
        $activity->content = $request->content;
        $activity->category = $request->category;

        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($activity->photo) {
                unlink(storage_path('app/public/' . $activity->photo));
            }

            // Store the new photo
            $photoPath = $request->file('photo')->store('photos', 'public');
            $activity->photo = $photoPath;
        }

        // Save the updated activity
        $activity->save();

        // Redirect back with a success message
        return redirect()->route('activities.show', $activity->id)->with('success', 'Activity updated successfully!');
    }

    // Remove the specified activity from storage
    public function destroy($id)
    {
        // Retrieve the activity by ID
        $activity = Activity::findOrFail($id);

        // Delete the photo if it exists
        $path = public_path('storage/' . $activity->photo);

        if (file_exists($path)) {
            unlink($path);
        }
        // Delete the activity
        $activity->delete();

        // Redirect back with a success message
        return redirect()->route('activities.index')->with('success', 'Activity deleted successfully!');
    }


}


