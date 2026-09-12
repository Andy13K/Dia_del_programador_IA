<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedUsers = $this->seedUsers();

        foreach ($seedUsers as $seedUser) {
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
     * OWASP A02/A07: las contraseñas nunca viven en el código versionado, ni siquiera como
     * valor de respaldo. Se leen de config('seed.*') — que a su vez solo lee de variables de
     * entorno (ver config/seed.php y .env.example) — y si falta alguna, el seeder falla ANTES
     * de tocar la tabla users, en vez de sembrar con una contraseña predecible.
     *
     * @return list<array{name: string, email: string, password: string, role: string}>
     */
    private function seedUsers(): array
    {
        $passwords = [
            'admin' => config('seed.admin_password'),
            'operador' => config('seed.operador_password'),
            'evaluador' => config('seed.evaluador_password'),
        ];

        $missing = array_keys(array_filter($passwords, fn (?string $value): bool => $value === null || $value === ''));

        if ($missing !== []) {
            throw new RuntimeException(
                'Faltan las contraseñas de usuarios semilla: '.implode(', ', $missing).'. '.
                'Definí SEED_ADMIN_PASSWORD, SEED_OPERADOR_PASSWORD y SEED_EVALUADOR_PASSWORD '.
                'en tu .env antes de sembrar (ver .env.example). No hay valor por defecto, '.
                'ni siquiera en desarrollo local.'
            );
        }

        return [
            ['name' => 'Administrador Nacional', 'email' => 'admin@solarguatemala.gob.gt', 'password' => $passwords['admin'], 'role' => 'admin'],
            ['name' => 'Operador Regional', 'email' => 'operador@solarguatemala.gob.gt', 'password' => $passwords['operador'], 'role' => 'operador'],
            ['name' => 'Evaluador Jurado', 'email' => 'evaluador@umg.edu.gt', 'password' => $passwords['evaluador'], 'role' => 'visualizador'],
        ];
    }
}
