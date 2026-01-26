<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsKasir
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'kasir') {
            return $next($request);
        }

        // Only redirect to admin if specifically an admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect('/admin');
        }

        // Otherwise (unknown role) go to home to avoid loops
        return redirect('/');
    }
}
