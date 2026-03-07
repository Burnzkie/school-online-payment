<?php
// app/Http/Middleware/EnsureCashier.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCashier
{
    /**
     * Restrict access to users with the 'cashier' role only.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'cashier') {
            abort(403, 'Access denied. Cashier role required.');
        }

        return $next($request);
    }
}