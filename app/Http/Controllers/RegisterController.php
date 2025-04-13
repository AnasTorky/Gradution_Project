<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
class RegisterController extends Controller
{
    public function create(){
        return view("auth.register");
    }
    public function store(Request $request){
        $attributes=request()->validate([
            'name'=> 'required',
            'email'=> ['required','email'],
            'password'=> ['required',Password::default(),'confirmed'],
        ]);
       $user= User::create($attributes);
       Auth::login($user);
        return redirect("/");
    }

}
