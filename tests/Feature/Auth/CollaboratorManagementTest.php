<?php

namespace Tests\Feature\Auth;

use App\Models\Collaborator;
use App\Models\State;
use App\Models\User;
use App\Support\BlindIndex;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CollaboratorManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_user_can_create_a_collaborator(): void
    {
        $user = User::factory()->create();
        $state = $this->state();

        $this->actingAs($user)
            ->post(route('collaborators.store'), $this->validPayload($state))
            ->assertRedirectToRoute('collaborators.index')
            ->assertSessionHas('status', 'Colaborador creado correctamente.');

        $collaborator = Collaborator::query()->firstOrFail();

        $this->assertSame($user->getKey(), $collaborator->user_id);
        $this->assertSame('Ana Pérez', $collaborator->nombre);
        $this->assertSame('ana@example.com', $collaborator->correo);
        $this->assertSame(BlindIndex::email('ana@example.com'), $collaborator->correo_hash);
        $this->assertSame(BlindIndex::rfc('PERA900101AB1'), $collaborator->rfc_hash);
        $this->assertSame('2024-01-15', $collaborator->fecha_inicio_laboral->format('Y-m-d'));
    }

    public function test_collaborator_email_must_be_unique_for_each_account(): void
    {
        $user = User::factory()->create();
        $state = $this->state();

        $this->actingAs($user)->post(route('collaborators.store'), $this->validPayload($state));

        $this->actingAs($user)
            ->post(route('collaborators.store'), [
                ...$this->validPayload($state),
                'correo' => 'ANA@EXAMPLE.COM',
                'rfc' => 'PELA900101AB1',
            ])
            ->assertSessionHasErrors([
                'correo' => 'Ya existe un colaborador con este correo.',
            ]);
    }

    public function test_user_cannot_view_another_accounts_collaborator(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create(['role_id' => $owner->role_id]);
        $state = $this->state();

        $this->actingAs($owner)->post(route('collaborators.store'), $this->validPayload($state));

        $this->actingAs($otherUser)
            ->get(route('collaborators.show', Collaborator::query()->firstOrFail()))
            ->assertNotFound();
    }

    public function test_store_returns_specific_messages_for_invalid_curp_values(): void
    {
        $user = User::factory()->create();
        $state = $this->state();

        $cases = [
            'PERA900101MQTRNN0' => 'La CURP debe tener 18 caracteres y una estructura válida.',
            'PERA900101MZZRNN01' => 'La entidad federativa contenida en la CURP no es válida.',
            'PERA900231MQTRNN01' => 'La fecha de nacimiento contenida en la CURP no es válida.',
            'PERA900101MQTRNN00' => 'El dígito verificador de la CURP no es válido.',
        ];

        foreach ($cases as $curp => $message) {
            $this->actingAs($user)
                ->post(route('collaborators.store'), [
                    ...$this->validPayload($state),
                    'curp' => $curp,
                ])
                ->assertSessionHasErrors(['curp' => $message]);
        }

        $this->assertDatabaseCount('collaborators', 0);
    }

    public function test_update_accepts_its_own_email_and_rfc_after_normalization(): void
    {
        $user = User::factory()->create();
        $state = $this->state();

        $this->actingAs($user)->post(route('collaborators.store'), $this->validPayload($state));

        $collaborator = Collaborator::query()->firstOrFail();

        $this->actingAs($user)
            ->put(route('collaborators.update', $collaborator), [
                ...$this->validPayload($state),
                'correo' => ' ANA@EXAMPLE.COM ',
                'rfc' => ' pera900101ab1 ',
                'curp' => ' pera-900101mqtrnn04 ',
            ])
            ->assertRedirectToRoute('collaborators.show', $collaborator)
            ->assertSessionHas('status', 'Colaborador actualizado correctamente.');

        $collaborator->refresh();

        $this->assertSame('ana@example.com', $collaborator->correo);
        $this->assertSame('PERA900101AB1', $collaborator->rfc);
        $this->assertSame('PERA900101MQTRNN04', $collaborator->curp);
    }

    public function test_update_returns_duplicate_and_curp_messages(): void
    {
        $user = User::factory()->create();
        $state = $this->state();

        $this->actingAs($user)->post(route('collaborators.store'), $this->validPayload($state));

        $first = Collaborator::query()->firstOrFail();

        $this->actingAs($user)->post(route('collaborators.store'), [
            ...$this->validPayload($state),
            'correo' => 'otra@example.com',
            'rfc' => 'LOPA900101AB1',
        ]);

        $this->actingAs($user)
            ->put(route('collaborators.update', $first), [
                ...$this->validPayload($state),
                'correo' => 'OTRA@EXAMPLE.COM',
                'rfc' => 'LOPA900101AB1',
                'curp' => 'PERA900231MQTRNN01',
            ])
            ->assertSessionHasErrors([
                'correo' => 'Ya existe un colaborador con este correo.',
                'rfc' => 'Ya existe un colaborador con este RFC.',
                'curp' => 'La fecha de nacimiento contenida en la CURP no es válida.',
            ]);
    }

    public function test_store_returns_the_configured_messages_for_required_and_invalid_fields(): void
    {
        $user = User::factory()->create();
        $state = $this->state();

        $this->actingAs($user)
            ->post(route('collaborators.store'), [
                ...$this->validPayload($state),
                'nombre' => '',
                'correo' => 'correo-invalido',
                'rfc' => 'ABCD991399XYZ',
                'domicilio_fiscal' => '',
                'numero_seguridad_social' => '123',
                'fecha_inicio_laboral' => 'fecha-invalida',
                'salario_diario' => '-1',
                'salario' => 'texto',
                'state_id' => 999,
            ])
            ->assertSessionHasErrors([
                'nombre' => 'Escribe el nombre del colaborador.',
                'correo' => 'Escribe un correo electrónico válido.',
                'rfc' => 'El RFC debe tener 12 o 13 caracteres y una estructura válida.',
                'domicilio_fiscal' => 'Escribe el domicilio fiscal.',
                'numero_seguridad_social' => 'El número de seguridad social debe contener 11 dígitos.',
                'fecha_inicio_laboral' => 'La fecha de inicio laboral no es válida.',
                'salario_diario' => 'El salario diario no puede ser negativo.',
                'salario' => 'El salario debe ser numérico.',
                'state_id' => 'El estado seleccionado no es válido.',
            ]);
    }

    /** @return array<string, int|string> */
    private function validPayload(State $state): array
    {
        return [
            'nombre' => 'Ana Pérez',
            'correo' => 'ana@example.com',
            'rfc' => 'PERA900101AB1',
            'domicilio_fiscal' => 'Av. Universidad 100, Querétaro',
            'curp' => 'PERA900101MQTRNN04',
            'numero_seguridad_social' => '12345678901',
            'fecha_inicio_laboral' => '2024-01-15',
            'tipo_contrato' => 'Indeterminado',
            'departamento' => 'Tecnología',
            'puesto' => 'Desarrolladora',
            'salario_diario' => '750.00',
            'salario' => '22500.00',
            'state_id' => $state->getKey(),
        ];
    }

    private function state(): State
    {
        return State::query()->create([
            'clave' => 'QUE',
            'clave_curp' => 'QT',
            'nombre' => 'Querétaro',
        ]);
    }
}
