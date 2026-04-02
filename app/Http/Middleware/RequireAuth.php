<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'Anda harus login terlebih dahulu.'], 401);
        }
        return $next($request);
    }
}
