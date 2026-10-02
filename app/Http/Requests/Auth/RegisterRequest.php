<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\Rfc;
use App\Support\BlindIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:25'],
            'email' => ['required', 'email:rfc', 'max:25'],
            'rfc' => ['required', new Rfc],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $validator->errors()->has('email') && User::query()
                    ->where('email_hash', BlindIndex::email($this->string('email')->toString()))
                    ->exists()) {
                    $validator->errors()->add('email', 'Ya existe una cuenta con este correo.');
                }

                if (! $validator->errors()->has('rfc') && User::query()
                    ->where('rfc_hash', BlindIndex::rfc($this->string('rfc')->toString()))
                    ->exists()) {
                    $validator->errors()->add('rfc', 'Ya existe una cuenta con este RFC.');
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Escribe tu nombre.',
            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'rfc.required' => 'Escribe tu RFC.',
            'password.required' => 'Crea una contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir al menos una letra.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->string('name')->toString()),
            'email' => BlindIndex::normalizeEmail($this->string('email')->toString()),
            'rfc' => BlindIndex::normalizeRfc($this->string('rfc')->toString()),
        ]);
    }
}
