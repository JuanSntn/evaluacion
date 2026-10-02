<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_account_update_rejects_an_rfc_with_an_invalid_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => 'Nombre Actualizado',
                'email' => 'nuevo@example.com',
                'rfc' => 'ABCD991399XYZ',
            ])
            ->assertRedirect(route('account.edit'))
            ->assertSessionHasErrors([
                'rfc' => 'El RFC debe tener 12 o 13 caracteres y una estructura válida.',
            ]);
    }

    public function test_account_update_rejects_an_invalid_email(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => 'Nombre Actualizado',
                'email' => 'correo-invalido',
                'rfc' => 'ABCD991231XYZ',
            ])
            ->assertRedirect(route('account.edit'))
            ->assertSessionHasErrors([
                'email' => 'Escribe un correo electrónico válido.',
            ]);
    }

    public function test_account_update_adds_https_to_a_website_without_a_protocol(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => 'Nombre Actualizado',
                'email' => 'nuevo@example.com',
                'rfc' => 'ABCD991231XYZ',
                'website' => 'www.google.com',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Información de la cuenta actualizada.');

        $this->assertSame('https://www.google.com', $user->fresh()->website);
    }

    public function test_account_update_rejects_an_invalid_website(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => 'Nombre Actualizado',
                'email' => 'nuevo@example.com',
                'rfc' => 'ABCD991231XYZ',
                'website' => 'sitio inválido',
            ])
            ->assertRedirect(route('account.edit'))
            ->assertSessionHasErrors([
                'website' => 'Escribe una dirección web válida que inicie con http:// o https://.',
            ]);
    }
}
