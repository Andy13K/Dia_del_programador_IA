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
     * Usuarios semilla congelados en docs/06-CONTRATOS-HORA-1.md §5.
     *
     * @var list<array{name: string, email: string, password: string, role: string}>
     */
    private const SEED_USERS = [
        ['name' => 'Administrador Nacional', 'email' => 'admin@solarguatemala.gob.gt', 'password' => 'Solar2026!Admin', 'role' => 'admin'],
        ['name' => 'Operador Regional', 'email' => 'operador@solarguatemala.gob.gt', 'password' => 'Operador2026!', 'role' => 'operador'],
        ['name' => 'Evaluador Jurado', 'email' => 'evaluador@umg.edu.gt', 'password' => 'Evaluador2026!', 'role' => 'visualizador'],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (self::SEED_USERS as $seedUser) {
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
}
