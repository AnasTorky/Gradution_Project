<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $latestVideo = $user->videos()->latest()->first();
        $resultValue = 'normal';

        if ($latestVideo && $latestVideo->result) {
            $resultValue = $latestVideo->result->result;
        }

        $query = Category::query();

        if ($resultValue === 'normal') {
            $query->where('name', '!=', 'Communication');
        }

        $categories = $query->get();

        return response()->json(['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('categories', 'public');
        }

        $category = Category::create($validated);

        return response()->json(['message' => 'Category created successfully!', 'category' => $category], 201);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json(['category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $category = Category::findOrFail($id);
        $category->fill($validated);

        if ($request->hasFile('photo')) {
            if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                Storage::disk('public')->delete($category->photo);
            }
            $category->photo = $request->file('photo')->store('categories', 'public');
        }

        $category->save();

        return response()->json(['message' => 'Category updated successfully!', 'category' => $category]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->photo && Storage::disk('public')->exists($category->photo)) {
            Storage::disk('public')->delete($category->photo);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully!']);
    }

    public function show_activity($id)
    {
        $category = Category::findOrFail($id);
        $activities = $category->activities;

        return response()->json([
            'category' => $category,
            'activities' => $activities
        ]);
    }
}
