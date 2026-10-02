<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\BlindIndex;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_password_can_be_recovered_with_matching_email_and_rfc(): void
    {
        $user = User::factory()->create([
            'email' => 'persona@example.com',
            'rfc' => 'PERA900101AB1',
            'password' => 'Anterior123',
        ]);

        $response = $this->post(route('password.recover'), [
            'email' => 'PERSONA@EXAMPLE.COM',
            'rfc' => 'pera 900101 ab1',
            'password' => 'Nueva1234',
            'password_confirmation' => 'Nueva1234',
        ]);

        $response->assertRedirectToRoute('login')
            ->assertSessionHas('status', 'Contraseña actualizada. Ya puedes iniciar sesión.');

        $this->assertTrue(Hash::check('Nueva1234', $user->fresh()->password));
    }

    public function test_password_recovery_uses_a_generic_error_for_non_matching_identity(): void
    {
        User::factory()->create([
            'email' => 'persona@example.com',
            'rfc' => 'PERA900101AB1',
            'password' => 'Anterior123',
        ]);

        $this->post(route('password.recover'), [
            'email' => 'persona@example.com',
            'rfc' => 'OTRA900101AB1',
            'password' => 'Nueva1234',
            'password_confirmation' => 'Nueva1234',
        ])->assertSessionHasErrors([
            'email' => 'No pudimos validar una cuenta con esos datos.',
        ]);
    }

    public function test_authenticated_user_can_update_account_information_and_blind_indexes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('account.update'), [
            'name' => 'Nombre Actualizado',
            'email' => 'nuevo@example.com',
            'rfc' => 'NUEV900101AB1',
            'address' => 'Av. Reforma 100',
            'phone' => '5512345678',
            'website' => 'https://example.com',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('status', 'Información de la cuenta actualizada.');

        $freshUser = $user->fresh();
        $this->assertSame('Nombre Actualizado', $freshUser->name);
        $this->assertSame('nuevo@example.com', $freshUser->email);
        $this->assertSame(BlindIndex::email('nuevo@example.com'), $freshUser->email_hash);
        $this->assertSame(BlindIndex::rfc('NUEV900101AB1'), $freshUser->rfc_hash);
    }

    public function test_authenticated_user_can_change_password_with_the_current_password(): void
    {
        $user = User::factory()->create(['password' => 'Anterior123']);

        $response = $this->actingAs($user)->put(route('account.password.update'), [
            'current_password' => 'Anterior123',
            'password' => 'Nueva1234',
            'password_confirmation' => 'Nueva1234',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('password_status', 'Contraseña actualizada. Cerramos cualquier otra sesión activa.');

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('Nueva1234', $user->fresh()->password));
    }

    public function test_password_change_rejects_an_incorrect_current_password(): void
    {
        $user = User::factory()->create(['password' => 'Anterior123']);

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'Incorrecta123',
                'password' => 'Nueva1234',
                'password_confirmation' => 'Nueva1234',
            ])
            ->assertSessionHasErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ]);

        $this->assertTrue(Hash::check('Anterior123', $user->fresh()->password));
    }

    public function test_password_change_rejects_a_password_without_the_required_policy(): void
    {
        $user = User::factory()->create(['password' => 'Anterior123']);

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'Anterior123',
                'password' => 'corta',
                'password_confirmation' => 'corta',
            ])
            ->assertSessionHasErrors([
                'password' => 'La contraseña debe tener al menos 8 caracteres.',
            ]);
    }
}
