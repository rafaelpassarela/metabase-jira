<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Projects>
 */
class ProjectsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        $keyLimit = random_int(2, 5);
        return [
            'project_key' => strtoupper(substr($name, 0, $keyLimit)),
            'displayName' => $name,
            'code' => fake()->numberBetween(0, 999999),
            'url' => fake()->url(),
            'avatar' => fake()->imageUrl(48, 48)
        ];
    }
}
