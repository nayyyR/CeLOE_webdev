<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    use ApiResponse;

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        $hasPermission = $user->role
            ->permissions()
            ->where('name', $permission)
            ->exists();

        if (! $hasPermission) {
            return $this->errorResponse('You do not have permission to perform this action.', 403);
        }

        return $next($request);
    }
}
