<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Dashboard stats
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'adminCount' => User::where('role', 'admin')->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ], 200);
    }

    // List all users
    public function users()
    {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200);
    }

    // List all activities
    public function activities()
    {
        $activities = Activity::all();
        return response()->json([
            'status' => 'success',
            'data' => $activities
        ], 200);
    }

    // List all categories
    public function categories()
    {
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], 200);
    }

    // Delete a user
    public function destroy_user(User $user)
    {
        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ], 200);
    }

    // Get a user's role
    public function editRole($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => ['id' => $user->id, 'role' => $user->role]
        ], 200);
    }

    // Update a user's role
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:0,1', // or 'admin,user' if roles are strings
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully',
            'data' => $user
        ], 200);
    }
}
