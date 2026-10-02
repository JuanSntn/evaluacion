<?php

namespace App\Http\Requests\ManagedUser;

use App\Models\ManagedUser;
use App\Rules\Rfc;
use App\Support\BlindIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreManagedUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255',],

            'rfc' => ['required', new Rfc,],

            'address' => ['required', 'string', 'max:500',],

            'phone' => ['required', 'regex:/^\d{10}$/',],

            'website' => [
                'nullable',
                'string',
                'url',
                'max:255',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('rfc')) {
                    return;
                }

                $rfcHash = BlindIndex::rfc(
                    $this->string('rfc')->toString()
                );

                $exists = ManagedUser::query()
                    ->where(
                        'created_by',
                        $this->user()->getKey()
                    )
                    ->where(
                        'rfc_hash',
                        $rfcHash
                    )
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'rfc',
                        'Ya existe un usuario con este RFC.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Escribe el nombre del usuario.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',

            'rfc.required' => 'Escribe el RFC.',

            'address.required' => 'Escribe la dirección.',
            'address.string' => 'La dirección debe ser texto.',
            'address.max' => 'La dirección no puede exceder 500 caracteres.',

            'phone.required' => 'Escribe el teléfono.',
            'phone.regex' => 'El teléfono debe contener 10 dígitos.',

            'website.url' => 'Escribe una dirección web válida.',
            'website.max' => 'El sitio web no puede exceder 255 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $website = trim($this->string('website')->toString());

        $this->merge([
            'name' => trim($this->string('name')->toString()),

            'rfc' => BlindIndex::normalizeRfc($this->string('rfc')->toString()),

            'address' => trim($this->string('address')->toString()),

            'phone' => preg_replace(
                '/[\s()-]+/',
                '',
                $this->string('phone')->toString()
            ),

            'website' => $website !== ''
                ? $website
                : null,
        ]);
    }
}
