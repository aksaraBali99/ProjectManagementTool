<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
        $name = fake()->unique()->name();

        return [
            'username' => fake()->unique()->userName(),
            'name' => $name,
            'employee_id' => fake()->unique()->numerify('EMP-#####'),
            'password' => static::$password ??= Hash::make('password'),
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->emails()->firstOrCreate([], ['email' => fake()->unique()->safeEmail(), 'label' => 'Email']);
            $user->phones()->firstOrCreate([], ['phone' => fake()->e164PhoneNumber(), 'label' => 'Phone number']);
        });
    }

    /**
     * Indicate that the model's account should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Give the user a specific email instead of a random one, replacing any
     * email the base factory configuration would otherwise generate.
     */
    public function withEmail(string $email): static
    {
        return $this->afterCreating(function (User $user) use ($email) {
            $user->emails()->delete();
            $user->emails()->create(['email' => $email, 'label' => 'Email']);
        });
    }

    /**
     * Give the user a specific phone number instead of a random one.
     */
    public function withPhone(string $phone): static
    {
        return $this->afterCreating(function (User $user) use ($phone) {
            $user->phones()->delete();
            $user->phones()->create(['phone' => $phone, 'label' => 'Phone number']);
        });
    }
}
