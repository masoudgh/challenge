<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route()->action['uses'];
        $user = $request->user();

        if ((!is_string($route))
            or (!in_array('auth:api', $request->route()->middleware(), true))
            or (!Str::of(($route))->contains('App\\Http\\Controllers\\'))
        ) {
            return $next($request);
        }

        // If the user has the sysop role, he/she will be allowed to access all sections
        if ($user->roles->pluck('name')->contains('sysop')) {
            return $next($request);
        }

        $permission = (string)Str::of($route)
            ->afterLast('App\\Http\\Controllers\\')
            ->replace('\\', '.')
            ->replace('@', '.')
            ->snake()
            ->replace('._', '.')
            ->replace('_controller', '')
            ->replace('_invoke', 'index');

        if ($user->can($permission)) {
            return $next($request);
        }

        abort(response()->json(['message' => 'User does not have the right permissions.'], Response::HTTP_FORBIDDEN));
    }
}
