<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        $user = Auth::user();

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

        if (strtolower($user->role->name) === 'employee') {
            $data['assignedToMe'] = Ticket::where('assigned_employee_id', $user->id)->count();
            $data['inProgressTickets'] = Ticket::where('assigned_employee_id', $user->id)->where('status', 'in_progress')->count();
            $data['resolvedTickets'] = Ticket::where('assigned_employee_id', $user->id)->where('status', 'resolved')->count();
            $data['recentAssigned'] = Ticket::where('assigned_employee_id', $user->id)
                ->with(['targetDivision', 'creator'])
                ->latest()
                ->take(5)
                ->get();
        }

        if (strtolower($user->role->name) === 'admin') {
            $data['totalTickets'] = Ticket::count();
            $data['openTickets'] = Ticket::where('status', 'open')->count();
            $data['inProgressTickets'] = Ticket::where('status', 'in_progress')->count();
            $data['resolvedTickets'] = Ticket::where('status', 'resolved')->count();
            $data['closedTickets'] = Ticket::where('status', 'closed')->count();
            $data['recentTickets'] = Ticket::with(['targetDivision', 'creator', 'assignedEmployee'])
                ->latest()
                ->take(5)
                ->get();
        }

        if (strtolower($user->role->name) === 'super admin') {
            $data['totalTickets'] = Ticket::count();
            $data['openTickets'] = Ticket::where('status', 'open')->count();
            $data['inProgressTickets'] = Ticket::where('status', 'in_progress')->count();
            $data['resolvedTickets'] = Ticket::where('status', 'resolved')->count();
            $data['closedTickets'] = Ticket::where('status', 'closed')->count();
            $data['totalUsers'] = User::count();
            $data['totalDivisions'] = Division::count();
            $data['recentTickets'] = Ticket::with(['targetDivision', 'creator', 'assignedEmployee'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view($viewName, $data);
    }
}
