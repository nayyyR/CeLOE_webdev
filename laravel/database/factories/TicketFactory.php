<?php

namespace Database\Factories;

use App\Models\Division;
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
        $divisionId = Division::query()->inRandomOrder()->value('id');

        $creatorId = User::query()
            ->where('division_id', $divisionId)
            ->inRandomOrder()
            ->value('id');

        $assigneeId = User::query()
            ->where('division_id', $divisionId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Employee');
            })
            ->inRandomOrder()
            ->value('id');

        return [
            'ticket_number' => 'TKT-'.fake()->unique()->numerify('######'),
            'subject' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement([
                'low',
                'medium',
                'high',
                'urgent',
            ]),
            'status' => 'open',
            'created_by' => $creatorId,
            'division_id' => $divisionId,
            'assigned_to' => $assigneeId,
            'resolved_at' => null,
            'closed_at' => null,
            'last_user_response_at' => now(),
            'last_activity_at' => now(),
        ];
    }
}
