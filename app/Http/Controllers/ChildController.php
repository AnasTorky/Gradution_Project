<?php
namespace App\Http\Controllers;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChildController extends Controller
{
   

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'skill' => 'nullable|string',
            'preferred_activities' => 'nullable|string',
        ]);
    
        $child = Child::create([
            'user_id' => Auth::id(), // This is critical
            'name' => $request->name,
            'age' => $request->age,
            'skill' => $request->skill,
            'preferred_activities' => $request->preferred_activities,
        ]);
    
        return response()->json($child, 201);
    }
    

    public function update(Request $request, Child $child)
    {
        // التحقق من أن المستخدم لديه صلاحية لتعديل الطفل
        if ($child->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
        ]);

        $child->update([
            'name' => $request->name,
            'age' => $request->age,
        ]);

        return response()->json([
            'child' => $child,
            'message' => 'Child updated successfully',
        ]);
    }

    public function destroy(Child $child)
    {
        // التحقق من أن المستخدم لديه صلاحية لحذف الطفل
        if ($child->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $child->delete();

        return response()->json([
            'message' => 'Child deleted successfully',
        ]);
    }
}
