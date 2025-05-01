<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Child;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // أضف هذا السطر

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        // تسجيل البيانات الواردة
        Log::info('Registration request data:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
//child data
            'childFullname' => 'nullable|string|max:255',
            'childAge' => 'nullable|integer',
            'skill' => 'nullable|string|max:255',
            'preferredActivities' => 'nullable|string|max:255',
        ]);

        Log::info('Validated data:', $validated);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        if (!empty($validated['childFullname'])) {
            Child::create([
                'user_id' => $user->id,
                'name' => $validated['childFullname'],
                'age' => $validated['childAge'],
                'skill' => $validated['skill'],
                'preferred_activities' => $validated['preferredActivities'],
            ]);
        }

        Log::info('User created successfully:', $user->toArray());

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }
}
