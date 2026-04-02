<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'Anda harus login terlebih dahulu.'], 401);
        }
        if (session('role') !== 'admin') {
            return response()->json(['error' => 'Akses ditolak. Hanya admin yang diizinkan.'], 403);
        }
        return $next($request);
    }
}
