<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_users_index(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_operador_cannot_access_users_index(): void
    {
        $operador = User::factory()->create(['role' => 'operador']);
        $response = $this->actingAs($operador)->get(route('users.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_users_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('users.index'));
        $response->assertOk();
        $response->assertSee('Administración de Usuarios y Roles');
    }

    public function test_admin_can_create_new_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Técnico Especialista',
            'email' => 'tecnico@solar.gob.gt',
            'role' => 'operador',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'tecnico@solar.gob.gt',
            'role' => 'operador',
        ]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
