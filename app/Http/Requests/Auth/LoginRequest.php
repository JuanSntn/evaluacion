<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Support\BlindIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class LoginRequest extends FormRequest
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
            'password' => ['required', 'string'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['email', 'password'])) {
                    return;
                }

                if ($this->authenticatedUser() === null) {
                    $validator->errors()->add('email', 'El correo o la contraseña no son correctos.');
                }
            },
        ];
    }

    public function authenticatedUser(): ?User
    {
        if ($this->userResolved) {
            return $this->resolvedUser;
        }

        $this->userResolved = true;
        $user = User::query()
            ->where('email_hash', BlindIndex::email($this->string('email')->toString()))
            ->first();

        $this->resolvedUser = $user !== null && Hash::check($this->string('password')->toString(), $user->password)
            ? $user
            : null;

        return $this->resolvedUser;
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'email.required' => 'Escribe tu correo electrónico.',
            'email.email' => 'Escribe un correo electrónico válido.',
            'password.required' => 'Escribe tu contraseña.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => BlindIndex::normalizeEmail($this->string('email')->toString()),
        ]);
    }
}
