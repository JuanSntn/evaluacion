<?php

namespace App\Http\Requests\Collaborator;

use App\Models\Collaborator;
use App\Rules\Curp;
use App\Rules\Rfc;
use App\Support\BlindIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCollaboratorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:25'],

            'correo' => ['required', 'email:rfc', 'max:25'],

            'rfc' => ['required', new Rfc],

            'domicilio_fiscal' => ['required', 'string', 'max:500'],

            'curp' => ['required', new Curp],

            'numero_seguridad_social' => ['required', 'regex:/^\d{11}$/'],

            'fecha_inicio_laboral' => ['required', 'date'],

            'tipo_contrato' => ['required', 'string', 'max:100'],

            'departamento' => ['required', 'string', 'max:100'],

            'puesto' => ['required', 'string', 'max:100'],

            'salario_diario' => [
                'required',
                'numeric',
                'min:0',
            ],

            'salario' => [
                'required',
                'numeric',
                'min:0',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('correo')) {
                    return;
                }

                $correoHash = BlindIndex::email(
                    $this->string('correo')->toString()
                );

                $exists = Collaborator::query()
                    ->where('user_id', $this->user()->getKey())
                    ->where('correo_hash', $correoHash)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'correo',
                        'Ya existe un colaborador con este correo.'
                    );
                }
            },

            function (Validator $validator): void {
                if ($validator->errors()->has('rfc')) {
                    return;
                }

                $rfcHash = BlindIndex::rfc(
                    $this->string('rfc')->toString()
                );

                $exists = Collaborator::query()
                    ->where('user_id', $this->user()->getKey())
                    ->where('rfc_hash', $rfcHash)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'rfc',
                        'Ya existe un colaborador con este RFC.'
                    );
                }
            },

            function (Validator $validator): void {
                if ($validator->errors()->has('curp')) {
                    return;
                }

                $exists = Collaborator::query()
                    ->where(
                        'curp_hash',
                        BlindIndex::curp($this->string('curp')->toString())
                    )
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'curp',
                        'Ya existe un colaborador con esta CURP.'
                    );
                }
            },

            function (Validator $validator): void {
                if ($validator->errors()->has('numero_seguridad_social')) {
                    return;
                }

                $exists = Collaborator::query()
                    ->where(
                        'nss_hash',
                        BlindIndex::socialSecurityNumber(
                            $this->string('numero_seguridad_social')->toString()
                        )
                    )
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'numero_seguridad_social',
                        'Ya existe un colaborador con este número de seguridad social.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Escribe el nombre del colaborador.',
            'correo.required' => 'Escribe el correo electrónico.',
            'correo.email' => 'Escribe un correo electrónico válido.',
            'rfc.required' => 'Escribe el RFC.',
            'domicilio_fiscal.required' => 'Escribe el domicilio fiscal.',
            'curp.required' => 'Escribe la CURP.',
            'curp.size' => 'La CURP debe tener 18 caracteres.',
            'curp.regex' => 'La CURP solamente puede contener letras y números.',
            'numero_seguridad_social.required' => 'Escribe el número de seguridad social.',
            'numero_seguridad_social.regex' => 'El número de seguridad social debe contener 11 dígitos.',
            'fecha_inicio_laboral.required' => 'Selecciona la fecha de inicio laboral.',
            'fecha_inicio_laboral.date' => 'La fecha de inicio laboral no es válida.',
            'tipo_contrato.required' => 'Escribe el tipo de contrato.',
            'departamento.required' => 'Escribe el departamento.',
            'puesto.required' => 'Escribe el puesto.',
            'salario_diario.required' => 'Escribe el salario diario.',
            'salario_diario.numeric' => 'El salario diario debe ser numérico.',
            'salario_diario.min' => 'El salario diario no puede ser negativo.',
            'salario.required' => 'Escribe el salario.',
            'salario.numeric' => 'El salario debe ser numérico.',
            'salario.min' => 'El salario no puede ser negativo.',
            'state_id.required' => 'Selecciona un estado.',
            'state_id.exists' => 'El estado seleccionado no es válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim($this->string('nombre')->toString()),

            'correo' => BlindIndex::normalizeEmail(
                $this->string('correo')->toString()
            ),

            'rfc' => BlindIndex::normalizeRfc(
                $this->string('rfc')->toString()
            ),

            'curp' => BlindIndex::normalizeCurp(
                $this->string('curp')->toString()
            ),

            'numero_seguridad_social' => BlindIndex::normalizeSocialSecurityNumber(
                $this->string('numero_seguridad_social')->toString()
            ),

            'tipo_contrato' => trim(
                $this->string('tipo_contrato')->toString()
            ),

            'departamento' => trim(
                $this->string('departamento')->toString()
            ),

            'puesto' => trim(
                $this->string('puesto')->toString()
            ),
        ]);
    }
}
