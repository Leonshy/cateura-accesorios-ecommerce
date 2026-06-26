<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Admin users pass all role checks
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        abort(403, 'Acceso no autorizado.');
    }
}
