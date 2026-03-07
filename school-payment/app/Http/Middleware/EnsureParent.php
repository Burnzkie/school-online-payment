<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureParent
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'parent') {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}