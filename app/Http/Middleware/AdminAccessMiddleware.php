<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->canAccessAdmin()) {
            abort(403, 'Acceso no autorizado al panel administrativo.');
        }
        return $next($request);
    }
}
