<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Rules\Rfc;
use App\Support\BlindIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class RecoverPasswordRequest extends FormRequest
{
    private bool $userResolved = false;

    private ?User $resolvedUser = null;

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
            'email' => ['required', 'email'],
            'rfc' => ['required', new Rfc],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['email', 'rfc'])) {
                    return;
                }

                if ($this->recoverableUser() === null) {
                    $validator->errors()->add('email', 'No pudimos validar una cuenta con esos datos.');
                }
            },
        ];
    }

    public function recoverableUser(): ?User
    {
        if ($this->userResolved) {
            return $this->resolvedUser;
        }

        $this->userResolved = true;
        $this->resolvedUser = User::query()
            ->where('email_hash', BlindIndex::email($this->string('email')->toString()))
            ->where('rfc_hash', BlindIndex::rfc($this->string('rfc')->toString()))
            ->first();

        return $this->resolvedUser;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'rfc.required' => 'Escribe tu RFC.',
            'password.required' => 'Escribe tu nueva contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe incluir al menos una letra.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => BlindIndex::normalizeEmail($this->string('email')->toString()),
            'rfc' => BlindIndex::normalizeRfc($this->string('rfc')->toString()),
        ]);
    }
}
