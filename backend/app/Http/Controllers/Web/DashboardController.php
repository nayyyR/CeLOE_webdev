<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = auth()->user();

        $viewName = match (strtolower($user->role->name)) {
            'user' => 'dashboard.user',
            'employee' => 'dashboard.employee',
            'admin' => 'dashboard.admin',
            'super admin' => 'dashboard.superadmin',
            default => 'dashboard.user',
        };

        return view($viewName);
    }
}
