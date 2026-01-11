<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
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
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Candidato Tehokas',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $healthyProject = Project::factory()->create([
            'name' => 'Projeto Saudável (Exemplo)',
            'description' => 'Este projeto está em dia. Todas as tarefas estão com prazo futuro ou concluídas.'
        ]);
        
        Task::factory(5)->create([
            'project_id' => $healthyProject->id,
            'status' => 'pending',
            'deadline' => now()->addDays(5),
        ]);

        $alertProject = Project::factory()->create([
            'name' => 'Projeto em Atraso (Crítico)',
            'description' => 'Este projeto tem muitas tarefas atrasadas e deve mostrar o alerta vermelho.'
        ]);

        Task::factory(3)->create([
            'project_id' => $alertProject->id,
            'status' => 'pending',
            'deadline' => now()->subDays(2),
        ]);
        
        Task::factory(2)->create([
            'project_id' => $alertProject->id,
            'status' => 'in_progress',
            'deadline' => now()->addDays(2),
        ]);
    }
}
