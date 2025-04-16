<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'adminCount' => User::where('role', 'admin')->count(),
        ];
        return view('admin.dashboard', $stats);
    }
    public function users()
    {
        $users = User::all();
        return view('admin.users-info', compact('users'));
    }
    public function activities()
    {
        $activities = Activity::all();
        return view('admin.activities-info', compact('activities'));
    }
    public function categories()
    {
        $categories = Category::all();
        return view('admin.categories-info', compact('categories'));
    }
    public function destroy_user(User $user) {
        $user->delete();
        $user = Auth::id();
        return redirect()->route('users',$user);
    }
    public function editRole($id)
{
    $user = User::findOrFail($id);
    return view('admin.edit-role', compact('user'));
}

public function updateRole(Request $request, $id)
{
    $request->validate([
        'role' => 'required|in:0,1', // or your actual roles
    ]);

    $user = User::findOrFail($id);
    $user->role = $request->role;
    $user->save();

    return redirect()->route('users')->with('success', 'Role updated successfully!');
}


}
