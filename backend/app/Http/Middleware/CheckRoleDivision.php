<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleDivision
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $role = strtolower($user->role->name);
        $division = strtolower($user->division->name);

        $valid = match (true) {
            $role === 'user' && $division === 'general' => true,
            $role === 'employee' && $division !== 'general' => true,
            $role === 'admin' && $division === 'general' => true,
            $role === 'super admin' && $division === 'general' => true,
            default => false,
        };

        if (! $valid) {
            return $this->errorResponse('Your role and division combination is not authorized.', 403);
        }

        return $next($request);
    }
}
