<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'column_id' => function (array $attributes) {
                // If project_id is provided as an integer, find it. If factory, create it.
                // Simple version: Create a stand-alone column linked to the project
                return null; // We can't easily auto-create without complex logic. 
                // Better approach: In tests, usually we create project first. 
                // Let's rely on tests providing it or nullable.
            },
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => 'pending', 
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'deadline' => fake()->dateTimeBetween('-1 month', '+1 month'),
        ];
    }
}
