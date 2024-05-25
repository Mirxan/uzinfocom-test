<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiPermission
{
    private string $actionName = '';
    private string $controllerName = '';
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (auth()->check()) {

            if ($user->isAdmin()) {
                return $next($request);
            }

            $this->currentRoute();

            if ($user->hasPermission($this->controllerName, $this->actionName)) {
                return $next($request);
            }
        }

        return response()->errorResponse(message: 'Permission denied!', code: 403);
    }

    public function currentRoute(): void
    {
        $action = Route::currentRouteAction();
        $route = str_replace('App\Http\Controllers\Api\\', '', $action);
        $route = explode('@', $route);
        $this->controllerName = $route[0]; //controller
        $this->actionName = $route[1]; //action
    }
}
