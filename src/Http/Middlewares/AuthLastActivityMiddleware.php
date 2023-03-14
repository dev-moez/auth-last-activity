<?php

namespace DevMoez\AuthLastActivity\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use DevMoez\AuthLastActivity\Services\AuthLastActivityService;

class AuthLastActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() and config('auth-last-activity.enabled'))
        {
            (new AuthLastActivityService())->create($request);
        }
        return $next($request);
    }
}
