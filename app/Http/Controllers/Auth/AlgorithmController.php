<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Algorithm\PalindromeRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class AlgorithmController extends Controller
{
    public function index(): View
    {
        return view('algorithm.index');
    }

    // se reciben el request y lo mandamos a PalindromeRequest para validar las reglas.
    public function palindromes(PalindromeRequest $request): View
    {
        $words = $request->validated('words');

        $results = collect($words)
            ->map(function (string $word): array {
                $normalized = $this->normalize($word);

                return [
                    'word' => $word,
                    'normalized' => $normalized,
                    'is_palindrome' => $this->isPalindrome($normalized),
                ];
            })
            ->values()
            ->all();

        return view('algorithm.index', [
            'results' => $results,
            'submittedWords' => $words,
            'wordCount' => count($words),
        ]);
    }

    // normaliza la palabra eliminando espacios y convirtiendo a minúsculas
    private function normalize(string $word): string
    {
        return Str::lower(trim($word));
    }

    // función para determinar si una palabra es un palíndromo
    private function isPalindrome(string $word): bool
    {
        $characters = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);

        return $characters !== false && $characters === array_reverse($characters);
    }
}
