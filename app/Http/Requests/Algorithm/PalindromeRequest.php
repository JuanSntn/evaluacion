<?php

namespace App\Http\Requests\Algorithm;

use Illuminate\Foundation\Http\FormRequest;

class PalindromeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // reglas para validar el request.
    // se valida que se reciba un array de palabras
    // se valida que cada palabra sea un string, que tenga un máximo de 100 caracteres y que solo contenga letras.
    public function rules(): array
    {
        return [
            'words' => [
                'required',
                'array',
                'min:3',
                'max:20',
            ],

            'words.*' => [
                'required',
                'string',
                'max:100',
                'regex:/^\p{L}+$/u',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'words.required' => 'Debes capturar las palabras a evaluar.',

            'words.array' => 'Las palabras enviadas no tienen un formato válido.',

            'words.min' => 'Debes evaluar al menos 3 palabras.',

            'words.max' => 'Puedes evaluar un máximo de 20 palabras.',

            'words.*.required' => 'Todas las palabras son obligatorias.',

            'words.*.string' => 'Cada palabra debe ser texto.',

            'words.*.max' => 'Cada palabra puede tener un máximo de 100 caracteres.',

            'words.*.regex' => 'Las palabras solamente pueden contener letras.',
        ];
    }

    // Normaliza las palabras antes de ejecutar las reglas de validación.
    // Si "words" no es un arreglo, no realiza ningún cambio
    protected function prepareForValidation(): void
    {
        $words = $this->input('words');

        if (! is_array($words)) {
            return;
        }

        $this->merge([
            'words' => array_map(
                static fn (mixed $word): mixed => is_string($word)
                    ? trim($word)
                    : $word,
                $words
            ),
        ]);
    }
}
