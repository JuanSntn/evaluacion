<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirectToRoute('login');
    }

    public function test_login_page_is_available_to_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Bienvenido de vuelta')
            ->assertSee('¿Olvidaste tu contraseña?');
    }

    public function test_valid_credentials_start_a_single_user_session(): void
    {
        $user = User::factory()->create([
            'email' => 'persona@example.com',
            'password' => 'password',
        ]);

        DB::table('sessions')->insert([
            'id' => 'old-session',
            'user_id' => $user->getKey(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test browser',
            'payload' => 'old',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => ' PERSONA@EXAMPLE.COM ',
            'password' => 'password',
        ]);

        $response->assertRedirectToRoute('dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseMissing('sessions', ['id' => 'old-session']);
    }

    public function test_invalid_credentials_return_a_generic_message(): void
    {
        User::factory()->create([
            'email' => 'persona@example.com',
            'password' => 'password',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'persona@example.com',
                'password' => 'incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'El correo o la contraseña no son correctos.',
            ]);

        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirectToRoute('login');

        $this->assertGuest();
    }
}
