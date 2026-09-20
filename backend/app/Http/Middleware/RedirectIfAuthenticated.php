<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $roleName = strtolower($user->role->name ?? '');
            $divisionName = strtolower($user->division->name ?? '');

            $route = match (true) {
                $roleName === 'user' && $divisionName === 'general' => 'user.dashboard',
                $roleName === 'employee' && $divisionName !== 'general' => 'employee.dashboard',
                $roleName === 'admin' && $divisionName === 'general' => 'admin.dashboard',
                $roleName === 'super admin' && $divisionName === 'general' => 'superadmin.dashboard',
                default => null,
            };

            if ($route) {
                return redirect()->route($route);
            }
        }

        return $next($request);
    }
}
