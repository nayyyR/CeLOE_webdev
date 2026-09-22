<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = Role::query()->inRandomOrder()->first();

        $divisionId = Division::query()
            ->when(
                in_array(strtolower($role->name), ['user', 'admin', 'super admin'], true),
                fn ($query) => $query->where('name', 'general')
            )
            ->when(
                strtolower($role->name) === 'employee',
                fn ($query) => $query->where('name', '!=', 'general')
            )
            ->inRandomOrder()
            ->value('id');

        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role_id' => $role->id,
            'division_id' => $divisionId,
            'remember_token' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
