<?php

declare(strict_types=1);

namespace Database\Seeders;

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
        foreach ($this->seedUsers() as $seedUser) {
            $user = User::query()->updateOrCreate(
                ['email' => $seedUser['email']],
                ['name' => $seedUser['name'], 'password' => $seedUser['password']],
            );

            // "role" no está en $fillable (OWASP A01) — se asigna por atributo directo, no por mass assignment.
            $user->role = $seedUser['role'];
            $user->save();
        }

        $this->call(DepartmentSeeder::class);
        $this->call(SolarDemoSeeder::class);
    }

    /**
     * Usuarios semilla (correos y roles congelados en docs/06-CONTRATOS-HORA-1.md §5).
     *
     * OWASP A02/A07: las contraseñas ya NO viven como texto plano en el código versionado.
     * Se leen de variables de entorno (nunca commiteadas, ver .env.example) con un valor de
     * respaldo solo para desarrollo local — cualquier despliegue real (incluida la URL pública)
     * debe definir SEED_ADMIN_PASSWORD / SEED_OPERADOR_PASSWORD / SEED_EVALUADOR_PASSWORD en el
     * panel de variables de entorno de la plataforma con valores propios, distintos a los que
     * quedaron expuestos en el historial de git.
     *
     * @return list<array{name: string, email: string, password: string, role: string}>
     */
    private function seedUsers(): array
    {
        return [
            ['name' => 'Administrador Nacional', 'email' => 'admin@solarguatemala.gob.gt', 'password' => env('SEED_ADMIN_PASSWORD', 'Solar2026!Admin'), 'role' => 'admin'],
            ['name' => 'Operador Regional', 'email' => 'operador@solarguatemala.gob.gt', 'password' => env('SEED_OPERADOR_PASSWORD', 'Operador2026!'), 'role' => 'operador'],
            ['name' => 'Evaluador Jurado', 'email' => 'evaluador@umg.edu.gt', 'password' => env('SEED_EVALUADOR_PASSWORD', 'Evaluador2026!'), 'role' => 'visualizador'],
        ];
    }
}
