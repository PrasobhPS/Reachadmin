<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
//use Illuminate\Support\Facades\Log;

class ApiKeyOrAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check for API key
        $apiKey = $request->header('X-API-Key');
        if ($apiKey && $apiKey == config('app.api_key')) {
            return $next($request);
        }

        // Check for authenticated user
        //Log::info('Checking authenticated user via Sanctum: ' . Auth::guard('sanctum')->check());
        if (Auth::guard('sanctum')->check()) {
            //Log::info('request: ' . Auth::guard('sanctum')->user());
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
