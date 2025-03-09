<?php
namespace App\Http\Middleware;
use Closure;

class Cors {
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', 'http://localhost:3000' || 'http://localhost:5173');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $response;
    }
}
