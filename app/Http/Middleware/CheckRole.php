<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if ($user->role !== $role) {
                return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token không hợp lệ'], 401);
        }

        return $next($request);
    }
}
