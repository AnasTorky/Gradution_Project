<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $latestVideo = $user->videos()->latest()->first();

        $resultValue = 0; // Default

        if ($latestVideo && $latestVideo->result) {
            $resultValue = $latestVideo->result->result; // Access result through relationship
        }

        $query = Category::query();

        if ($resultValue == 0) {
            $query->where('name', '!=', 'Communication');
        }

        $categories = $query->get();

        return view('categories.index', compact('categories'));
    }
        public function create(){
            // return view('categories.create');
            dd('Reached Activity Create Page');
        }

        public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|max:255',
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

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }


        //
    //    public function show(Category $category)
    //     {
    //         // Get activities related to this category with pagination
    //         $activities = $category->activities()->paginate(10); // Adjust per page count as needed

    //         return view('categories.show', compact('category', 'activities'));
    //     }
        public function edit($id)
        {
            $category = Category::findOrFail($id);
            return view('categories.edit', compact('category'));
        }

        public function update(Request $request, $id)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'content' => 'required|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);


            $category = Category::findOrFail($id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->content = $request->content;

            if ($request->hasFile('photo')) {
                // Delete the old photo if it exists
                if ($category->photo) {
                    unlink(storage_path('app/public/' . $category->photo));
                }

                // Store the new photo
                $photoPath = $request->file('photo')->store('photos', 'public');
                $category->photo = $photoPath;
            }

            // Save the updated Category
            $category->save();

            // Redirect back with a success message
            return redirect()->route('admin.categories', $category->id)->with('success', 'Category updated successfully!');
        }

        // Remove the specified Category from storage
        public function destroy($id)
        {
            // Retrieve the Category by ID
            $category = Category::findOrFail($id);

            // Delete the photo if it exists
            $path = public_path('storage/' . $category->photo);

            if (file_exists($path)) {
                unlink($path);
            }
            // Delete the Category
            $category->delete();

            // Redirect back with a success message
            return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
        }
        public function show_activity($id)
    {
        $category = Category::find($id);
        $activities = Activity::where("category_id",$id)->get();
        return view('activities.index', compact('category', 'activities'));
    }

}
