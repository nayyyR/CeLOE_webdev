<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
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

        $data = [];

        if (strtolower($user->role->name) === 'user') {
            $data['totalTickets'] = Ticket::where('created_by', $user->id)->count();
            $data['openTickets'] = Ticket::where('created_by', $user->id)->where('status', 'open')->count();
            $data['inProgressTickets'] = Ticket::where('created_by', $user->id)->where('status', 'in_progress')->count();
            $data['resolvedTickets'] = Ticket::where('created_by', $user->id)->where('status', 'resolved')->count();
            $data['closedTickets'] = Ticket::where('created_by', $user->id)->where('status', 'closed')->count();
            $data['recentTickets'] = Ticket::where('created_by', $user->id)
                ->with('targetDivision')
                ->latest()
                ->take(5)
                ->get();
        }

        return view($viewName, $data);
    }
}
