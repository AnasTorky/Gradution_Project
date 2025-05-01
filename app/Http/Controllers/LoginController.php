<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request data
        $attributes = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to authenticate user
        if (!Auth::attempt($attributes)) {
            // Return 401 Unauthorized with a general error message
            return response()->json([
                'error' => 'Invalid credentials provided.',
            ], 401); // Changed to 401 for authentication failure
        }

        // If authentication is successful, get user and generate token
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return user and token information along with success message
        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful',
        ]);
    }

    // Logout function to invalidate the token
    public function destroy(Request $request)
    {
        // Delete all user's tokens, effectively logging them out
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
