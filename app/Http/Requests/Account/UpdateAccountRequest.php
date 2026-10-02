<?php

namespace App\Http\Requests\Account;

use App\Models\User;
use App\Rules\Rfc;
use App\Support\BlindIndex;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'rfc' => ['required', new Rfc],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $userId = $this->user()?->getKey();

                if (! $validator->errors()->has('email') && User::query()
                    ->whereKeyNot($userId)
                    ->where('email_hash', BlindIndex::email($this->string('email')->toString()))
                    ->exists()) {
                    $validator->errors()->add('email', 'Ya existe una cuenta con este correo.');
                }

                if (! $validator->errors()->has('rfc') && User::query()
                    ->whereKeyNot($userId)
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
            'website.url' => 'Escribe una dirección web válida que inicie con http:// o https://.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $website = trim($this->string('website')->toString());

        if ($website !== '' && ! preg_match('/\A[a-z][a-z\d+.-]*:\/\//i', $website)) {
            $website = 'https://'.$website;
        }

        $this->merge([
            'name' => trim($this->string('name')->toString()),
            'email' => BlindIndex::normalizeEmail($this->string('email')->toString()),
            'rfc' => BlindIndex::normalizeRfc($this->string('rfc')->toString()),
            'website' => $website !== '' ? $website : null,
        ]);
    }
}
