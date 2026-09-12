<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class DatabaseSeederCredentialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_fails_when_all_seed_passwords_are_missing(): void
    {
        config([
            'seed.admin_password' => null,
            'seed.operador_password' => null,
            'seed.evaluador_password' => null,
        ]);

        $this->expectException(RuntimeException::class);

        (new DatabaseSeeder)->run();
    }

    public function test_seeder_fails_when_only_one_seed_password_is_missing(): void
    {
        config([
            'seed.admin_password' => 'Str0ng#AdminPass',
            'seed.operador_password' => null,
            'seed.evaluador_password' => 'Str0ng#EvaluadorPass',
        ]);

        $this->expectException(RuntimeException::class);

        (new DatabaseSeeder)->run();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_seeder_creates_the_three_seed_users_with_hashed_passwords_when_configured(): void
    {
        config([
            'seed.admin_password' => 'Str0ng#AdminPass',
            'seed.operador_password' => 'Str0ng#OperadorPass',
            'seed.evaluador_password' => 'Str0ng#EvaluadorPass',
        ]);

        (new DatabaseSeeder)->run();

        $admin = User::query()->where('email', 'admin@solarguatemala.gob.gt')->firstOrFail();
        $operador = User::query()->where('email', 'operador@solarguatemala.gob.gt')->firstOrFail();
        $evaluador = User::query()->where('email', 'evaluador@umg.edu.gt')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertSame('operador', $operador->role);
        $this->assertSame('visualizador', $evaluador->role);

        $this->assertTrue(Hash::check('Str0ng#AdminPass', $admin->password));
        $this->assertTrue(Hash::check('Str0ng#OperadorPass', $operador->password));
        $this->assertTrue(Hash::check('Str0ng#EvaluadorPass', $evaluador->password));

        $this->assertStringNotContainsString('Str0ng#AdminPass', $admin->password);
        $this->assertStringNotContainsString('Str0ng#OperadorPass', $operador->password);
        $this->assertStringNotContainsString('Str0ng#EvaluadorPass', $evaluador->password);
    }
}
