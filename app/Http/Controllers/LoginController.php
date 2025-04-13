<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create(){
        return view("auth.login");
    }
    public function store(Request $request){
        $attributes=request()->validate([
            'email'=> ['required','email'],
            'password'=> ['required'],
        ]);
        if(!Auth::attempt($attributes)){
            // throw ValidationException::withMessages(['email'=> 'failed']);
            return redirect()->back()->withErrors(['email'=> 'failed','password'=> 'failed'])->withInput();
        };
        request()->session()->regenerate();
        return redirect("/");
    }
    public function destroy(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/");
    }
}
