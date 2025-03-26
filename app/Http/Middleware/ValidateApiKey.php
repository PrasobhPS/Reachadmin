<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        if(isset($apiKey)) {

            if ($apiKey !== Config::get('api.api_key')) {
                return response()->json(['error' => 'Invalid API key'], 401);
            }
        } else {
            return response()->json(['error' => 'Invalid API key'], 401);
        }
        return $next($request);
    }
}