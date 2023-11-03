<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Filters>
 */
class FiltersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $count = random_int(2, 5);
        $filter = 'project in (';
        for ($i = 0; $i < $count; $i++) {
            $keyLimit = random_int(2, 5);
            $name = fake()->company();
            $name = strtoupper(substr($name, 0, $keyLimit));
            $filter = $filter . "'$name', ";
        }

        $filter = substr($filter, 0, strlen($filter) - 2);
        $filter = $filter . ')';

        return [
            'description' => fake()->company(),
            'filter' => $filter,
            'done_filter' => 'done = true',
            'active' => 1
        ];
    }
}
