<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $userRoleName = strtolower($user->role->name ?? '');

        $allowed = collect($roles)->map(fn ($role) => strtolower($role))->contains($userRoleName);

        if (! $allowed) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
