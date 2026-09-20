<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DivisionController extends Controller
{
    public function index(): View
    {
        $divisions = Division::withCount(['users', 'tickets'])->latest()->get();

        return view('superadmin.divisions.index', compact('divisions'));
    }

    public function create(): View
    {
        return view('superadmin.divisions.create');
    }

    public function store(StoreDivisionRequest $request): RedirectResponse
    {
        Division::create($request->validated());

        return redirect()->route('superadmin.divisions.index')->with('success', 'Division created successfully.');
    }

    public function show(Division $division): View
    {
        $division->loadCount(['users', 'tickets']);

        return view('superadmin.divisions.show', compact('division'));
    }

    public function edit(Division $division): View
    {
        return view('superadmin.divisions.edit', compact('division'));
    }

    public function update(UpdateDivisionRequest $request, Division $division): RedirectResponse
    {
        $division->update($request->validated());

        return redirect()->route('superadmin.divisions.show', $division)->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division): RedirectResponse
    {
        if (strtolower($division->name) === 'general') {
            return redirect()->route('superadmin.divisions.index')
                ->with('error', 'The General division cannot be deleted because it is mandatory for Super Admin and Admin roles.');
        }

        DB::transaction(function () use ($division) {
            $ticketIds = Ticket::where('target_division_id', $division->id)->pluck('id');

            TicketThread::whereIn('ticket_id', $ticketIds)->delete();

            Ticket::whereIn('id', $ticketIds)->delete();

            User::where('division_id', $division->id)->delete();

            $division->delete();
        });

        return redirect()->route('superadmin.divisions.index')->with('success', 'Division and all associated data deleted successfully.');
    }
}
