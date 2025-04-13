<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
    public function destroy_user(User $user) {
        $user->delete();
        $user = Auth::id();
        return redirect()->route('users',$user);
    }
}
