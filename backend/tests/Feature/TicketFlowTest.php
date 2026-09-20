<?php

use App\Models\ActivityLog;
use App\Models\Division;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Clear MongoDB collections via Model (works without explicit connection config)
    TicketThread::query()->delete();
    ActivityLog::query()->delete();

    $this->userRole = Role::create(['name' => 'User']);
    $this->employeeRole = Role::create(['name' => 'Employee']);
    $this->adminRole = Role::create(['name' => 'Admin']);
    $this->superAdminRole = Role::create(['name' => 'Super Admin']);

    $this->generalDivision = Division::create(['name' => 'general']);
    $this->itDivision = Division::create(['name' => 'IT Support']);

    $this->createTickets = Permission::create(['name' => 'create_tickets']);
    $this->viewAllTickets = Permission::create(['name' => 'view_all_tickets']);
    $this->viewAssignedTickets = Permission::create(['name' => 'view_assigned_tickets']);
    $this->assignTickets = Permission::create(['name' => 'assign_tickets']);
    $this->resolveTickets = Permission::create(['name' => 'resolve_tickets']);
    $this->replyTickets = Permission::create(['name' => 'reply_tickets']);
    $this->confirmResolution = Permission::create(['name' => 'confirm_resolution']);

    $this->userRole->permissions()->attach([
        $this->createTickets->id,
        $this->confirmResolution->id,
    ]);

    $this->employeeRole->permissions()->attach([
        $this->viewAssignedTickets->id,
        $this->resolveTickets->id,
        $this->replyTickets->id,
    ]);

    $this->adminRole->permissions()->attach([
        $this->viewAllTickets->id,
        $this->assignTickets->id,
    ]);

    $this->user = User::create([
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'user@test.com',
        'password' => Hash::make('password'),
        'role_id' => $this->userRole->id,
        'division_id' => $this->generalDivision->id,
    ]);

    $this->employee = User::create([
        'name' => 'Test Employee',
        'username' => 'testemployee',
        'email' => 'employee@test.com',
        'password' => Hash::make('password'),
        'role_id' => $this->employeeRole->id,
        'division_id' => $this->itDivision->id,
    ]);

    $this->admin = User::create([
        'name' => 'Test Admin',
        'username' => 'testadmin',
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'role_id' => $this->adminRole->id,
        'division_id' => $this->generalDivision->id,
    ]);
});

describe('Ticket Lifecycle: Create -> Assign -> Resolve', function () {

    it('creates a ticket and populates MySQL and MongoDB', function () {
        $token = $this->user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/user/tickets', [
                'subject' => 'Laptop tidak bisa menyala',
                'description' => 'Laptop saya tiba-tiba mati setelah update Windows.',
                'target_division_id' => $this->itDivision->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'ticket_number', 'subject', 'status'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'subject' => 'Laptop tidak bisa menyala',
                    'status' => 'open',
                ],
            ]);

        $ticket = Ticket::where('subject', 'Laptop tidak bisa menyala')->first();
        expect($ticket)->not->toBeNull();
        expect($ticket->ticket_number)->toStartWith('TKT-');
        expect($ticket->status)->toBe('open');
        expect($ticket->created_by)->toBe($this->user->id);
        expect($ticket->target_division_id)->toBe($this->itDivision->id);

        $thread = TicketThread::where('ticket_id', $ticket->id)->first();
        expect($thread)->not->toBeNull();
        expect($thread->body)->toBe('Laptop saya tiba-tiba mati setelah update Windows.');
        expect($thread->user_id)->toBe($this->user->id);

        $activityLog = ActivityLog::where('ticket_id', $ticket->id)
            ->where('action', 'ticket_created')
            ->first();
        expect($activityLog)->not->toBeNull();
        expect($activityLog->old_status)->toBeNull();
        expect($activityLog->new_status)->toBe('open');
    });

    it('allows admin to assign a ticket and logs activity', function () {
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-000001',
            'subject' => 'Printer error',
            'description' => 'Printer di lantai 2 error.',
            'priority' => 'medium',
            'status' => 'open',
            'created_by' => $this->user->id,
            'target_division_id' => $this->itDivision->id,
            'assigned_employee_id' => null,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson("/api/admin/tickets/{$ticket->id}/assign", [
                'assigned_employee_id' => $this->employee->id,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['status' => 'in_progress'],
            ]);

        $ticket->refresh();
        expect($ticket->status)->toBe('in_progress');
        expect($ticket->assigned_employee_id)->toBe($this->employee->id);

        $activityLog = ActivityLog::where('ticket_id', $ticket->id)
            ->where('action', 'ticket_assigned')
            ->first();
        expect($activityLog)->not->toBeNull();
        expect($activityLog->old_status)->toBe('open');
        expect($activityLog->new_status)->toBe('in_progress');
    });

    it('allows employee to resolve a ticket and adds activity log', function () {
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-000002',
            'subject' => 'Network down',
            'description' => 'Jaringan lantai 3 mati total.',
            'priority' => 'high',
            'status' => 'in_progress',
            'created_by' => $this->user->id,
            'target_division_id' => $this->itDivision->id,
            'assigned_employee_id' => $this->employee->id,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        $token = $this->employee->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson("/api/employee/tickets/{$ticket->id}/status", [
                'status' => 'resolved',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => ['status' => 'resolved'],
            ]);

        $ticket->refresh();
        expect($ticket->status)->toBe('resolved');
        expect($ticket->resolved_at)->not->toBeNull();

        $activityLog = ActivityLog::where('ticket_id', $ticket->id)
            ->where('action', 'status_changed')
            ->first();
        expect($activityLog)->not->toBeNull();
        expect($activityLog->old_status)->toBe('in_progress');
        expect($activityLog->new_status)->toBe('resolved');
    });

    it('allows employee to add a thread to an assigned ticket', function () {
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-000003',
            'subject' => 'Software install',
            'description' => 'Perlu install Photoshop.',
            'priority' => 'low',
            'status' => 'in_progress',
            'created_by' => $this->user->id,
            'target_division_id' => $this->itDivision->id,
            'assigned_employee_id' => $this->employee->id,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        $token = $this->employee->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/employee/tickets/{$ticket->id}/threads", [
                'body' => 'Sudah diinstall, mohon dicek.',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $thread = TicketThread::where('ticket_id', $ticket->id)
            ->where('body', 'Sudah diinstall, mohon dicek.')
            ->first();
        expect($thread)->not->toBeNull();
        expect($thread->user_id)->toBe($this->employee->id);

        $activityLog = ActivityLog::where('ticket_id', $ticket->id)
            ->where('action', 'thread_added')
            ->first();
        expect($activityLog)->not->toBeNull();

        $ticket->refresh();
        expect($ticket->last_activity_at)->not->toBeNull();
    });

    it('runs the full lifecycle in sequence', function () {
        // Step 1: Create ticket directly (avoids DB::transaction + MongoDB hybrid in tests)
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-000010',
            'subject' => 'Server down',
            'description' => 'Server utama tidak merespons.',
            'priority' => 'high',
            'status' => 'open',
            'created_by' => $this->user->id,
            'target_division_id' => $this->itDivision->id,
            'assigned_employee_id' => null,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        // Step 2: Admin assigns ticket via API
        $this->actingAs($this->admin);
        $assignResponse = $this->patchJson("/api/admin/tickets/{$ticket->id}/assign", [
            'assigned_employee_id' => $this->employee->id,
        ]);

        $assignResponse->assertOk();

        // Step 3: Employee adds thread via API
        $this->actingAs($this->employee);
        $threadResponse = $this->postJson("/api/employee/tickets/{$ticket->id}/threads", [
            'body' => 'Sedang investigate, estimated 1 hour.',
        ]);

        $threadResponse->assertStatus(201);

        // Step 4: Employee resolves via API
        $resolveResponse = $this->patchJson("/api/employee/tickets/{$ticket->id}/status", [
            'status' => 'resolved',
        ]);

        $resolveResponse->assertOk();

        // Assertions
        $ticket->refresh();
        expect($ticket->status)->toBe('resolved');

        $threads = TicketThread::where('ticket_id', $ticket->id)->get();
        expect($threads)->toHaveCount(1);

        $logs = ActivityLog::where('ticket_id', $ticket->id)->get();
        expect($logs)->toHaveCount(3);

        $actions = $logs->pluck('action')->toArray();
        expect($actions)->toEqual([
            'ticket_assigned',
            'thread_added',
            'status_changed',
        ]);
    });

    it('rejects assignment when employee is from wrong division', function () {
        $wrongDivision = Division::create(['name' => 'HR']);
        $wrongEmployee = User::create([
            'name' => 'HR Employee',
            'username' => 'hremployee',
            'email' => 'hr@test.com',
            'password' => Hash::make('password'),
            'role_id' => $this->employeeRole->id,
            'division_id' => $wrongDivision->id,
        ]);

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-000004',
            'subject' => 'Laptop issue',
            'description' => 'Laptop bermasalah.',
            'priority' => 'medium',
            'status' => 'open',
            'created_by' => $this->user->id,
            'target_division_id' => $this->itDivision->id,
            'assigned_employee_id' => null,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson("/api/admin/tickets/{$ticket->id}/assign", [
                'assigned_employee_id' => $wrongEmployee->id,
            ]);

        $response->assertUnprocessable();
    });

    it('rejects status transition from open to resolved by employee', function () {
        $ticket = Ticket::create([
            'ticket_number' => 'TKT-000005',
            'subject' => 'Bug report',
            'description' => 'Aplikasi crash.',
            'priority' => 'high',
            'status' => 'open',
            'created_by' => $this->user->id,
            'target_division_id' => $this->itDivision->id,
            'assigned_employee_id' => $this->employee->id,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ]);

        $token = $this->employee->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson("/api/employee/tickets/{$ticket->id}/status", [
                'status' => 'resolved',
            ]);

        $response->assertStatus(422);
    });
});
