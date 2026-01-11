<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Candidato Tasso',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $user = User::where('email', 'test@example.com')->first();

        $healthyProject = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Projeto Saudável',
            'description' => 'Um projeto onde tudo está no prazo.',
        ]);

        $todoCol = $healthyProject->columns()->create(['name' => 'Pendente', 'order' => 1]);
        $doingCol = $healthyProject->columns()->create(['name' => 'Em Andamento', 'order' => 2]);
        $doneCol = $healthyProject->columns()->create(['name' => 'Concluída', 'order' => 3, 'is_completed' => true]);

        Task::factory(3)->create([
            'project_id' => $healthyProject->id,
            'column_id' => $todoCol->id,
            'status' => 'pending',
            'deadline' => now()->addDays(5),
        ]);

        Task::factory(2)->create([
            'project_id' => $healthyProject->id,
            'column_id' => $doneCol->id,
            'status' => 'completed',
            'deadline' => now()->subDay(),
        ]);

        $alertProject = Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Projeto em Alerta',
            'description' => 'Muitas tarefas atrasadas e não concluídas.',
        ]);

        $col1 = $alertProject->columns()->create(['name' => 'A Fazer', 'order' => 1]);
        $col2 = $alertProject->columns()->create(['name' => 'Fazendo', 'order' => 2]);
        $col3 = $alertProject->columns()->create(['name' => 'Feito', 'order' => 3, 'is_completed' => true]);

        Task::factory(2)->create([
            'project_id' => $alertProject->id,
            'column_id' => $col1->id,
            'status' => 'pending',
            'deadline' => now()->subDays(2),
        ]);

        Task::factory(3)->create([
            'project_id' => $alertProject->id,
            'column_id' => $col3->id,
            'status' => 'completed',
            'deadline' => now()->addDays(2),
        ]);
    }
}
