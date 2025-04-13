<?php

namespace App\Http\Controllers;
use App\Models\Child;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildController extends Controller
{
    public function create(){
        return view("children.create-child");
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1'
        ]);

        $child = new Child();
        $child->name = $request->name;
        $child->age = $request->age;
        $child->user_id = Auth::user()->id;
        $child->save();
        $user = Auth::id();
        return redirect()->route('profile.show',$user)->with('success', 'Child created successfully!');
    }
    public function edit(Child $child) {
        return view('children.edit-child', ['child' => $child]);
    }
    public function update(Request $request,Child $child) {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1'
        ]);
        $child->update([
            'name'=>request('name'),
            'age'=>request('age'),
        ]);
        $user = Auth::id();
        return redirect()->route('profile.show', $user);
    }
    public function destroy(Child $child) {
        $child->delete();
        $user = Auth::id();
        return redirect()->route('profile.show',$user);
    }
}
