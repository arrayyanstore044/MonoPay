<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'UnAuthorized.',
                'data' => null
            ], 401);
        }

        $session = UserSession::where('token', $token)
            ->where('expired_at', '>', now())
            ->first();

        if (!$session) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'UnAuthorized.',
                'data' => null
            ], 401);
        }

        // Set current user ke Auth agar bisa diakses di controller via Auth::user()
        Auth::login($session->user);

        return $next($request);
    }
}
