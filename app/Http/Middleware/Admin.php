<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class Admin
{
    public function handle(Request $request, Closure $next)
    {

    $user = Auth::user();

    if ($user && $user->role == 1) {
        return $next($request);
    }

    // Redirect to home or show error message if not an admin
    return redirect('/')->with('error', 'You do not have admin access.');
}


}
