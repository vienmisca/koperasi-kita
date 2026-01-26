<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // Only redirect to dashboard if specifically a kasir
        if (Auth::check() && Auth::user()->role === 'kasir') {
            return redirect('/dashboard');
        }

        // Otherwise (unknown role) go to home to avoid loops
        return redirect('/');
    }
}
