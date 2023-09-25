<?php

namespace Database\Factories;

use App\Helpers\GUID;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personas>
 */
class PersonasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'displayName' => fake()->name(),
            'accountId' => GUID::Generate(),
            'avatar' => fake()->imageUrl(48, 48),
            'active' => fake()->boolean(70)
        ];
    }
}
