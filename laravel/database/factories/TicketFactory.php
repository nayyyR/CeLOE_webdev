<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'Employee'))
            ->inRandomOrder()
            ->first();

        $creator = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'User'))
            ->inRandomOrder()
            ->first()
            ?? $employee;

        $status = fake()->randomElement([
            'open',
            'open',
            'open',
            'in_progress',
            'in_progress',
            'resolved',
            'resolved',
            'closed',
        ]);

        $createdAt = fake()->dateTimeBetween('-6 months');

        $assignedEmployeeId = in_array($status, ['in_progress', 'resolved', 'closed'], true)
            ? $employee->id
            : null;

        $resolvedAt = in_array($status, ['resolved', 'closed'], true)
            ? fake()->dateTimeBetween($createdAt)
            : null;

        $closedAt = $status === 'closed'
            ? fake()->dateTimeBetween($resolvedAt ?? $createdAt)
            : null;

        return [
            'ticket_number' => 'TKT-'.fake()->unique()->numerify('######'),
            'subject' => fake()->sentence(6),
            'description' => fake()->paragraph(3),
            'priority' => fake()->randomElement([
                'low',
                'medium',
                'high',
                'urgent',
            ]),
            'status' => $status,
            'created_by' => $creator->id,
            'target_division_id' => $employee->division_id,
            'assigned_employee_id' => $assignedEmployeeId,
            'resolved_at' => $resolvedAt,
            'closed_at' => $closedAt,
            'last_user_response_at' => fake()->dateTimeBetween($createdAt),
            'last_activity_at' => fake()->dateTimeBetween($createdAt),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
