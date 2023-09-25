<?php

namespace Database\Factories;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Issues>
 */
class IssuesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        throw new Exception("Issue seeder not implemented", 501);

        return array();
        // return [
        //     'keyJira' => fake()-name fake()->name(),
        //     'emailAddress' => fake()->unique()->safeEmail(),
        //     'accountId' => GUID::Generate(),
        //     'avatar' => fake()->imageUrl(48, 48),
        //     'active' => fake()->boolean(70)
        // ];


        // '',
        // 'project_id',
        // 'summary',
        // 'storyPoints',
        // 'resolvedAt',
        // 'resolution',
        // 'classe',
        // 'tema',
        // 'subTema',
        // 'areaDemandante',
        // 'parentKey',
        // 'sprintId',
        // 'status',
        // 'issueType'

    }
}
