<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthSession
{
    /**
     * Handle an incoming request.
     * Ensure the user is authenticated via session before accessing protected web routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan masuk terlebih dahulu untuk mengakses sistem.',
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        return $next($request);
    }
}
