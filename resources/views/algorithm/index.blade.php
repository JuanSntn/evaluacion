<x-layouts.app title="Algoritmos">
    @php
        $currentWords = old('words', $submittedWords ?? ['', '', '']);
        $currentWords = array_slice(array_pad($currentWords, 3, ''), 0, 20);
    @endphp

    <div class="mb-7 max-w-2xl">
        <p class="mb-1 text-sm text-gray-500">Algoritmos</p>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Detector de palíndromos</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">
            Escribe las palabras que deseas analizar. El sistema identificará cuáles se leen igual de izquierda a derecha.
        </p>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1fr_0.9fr] lg:items-start">
        <section class="panel" aria-labelledby="algorithm-heading">
            <div class="panel-heading">
                <p class="panel-kicker">Captura</p>
                <h2 id="algorithm-heading" class="text-2xl font-semibold tracking-[-0.025em]">Palabras a evaluar</h2>
                <p class="mt-2 text-sm leading-6 text-gray-600">
                    Completa al menos tres palabras para ejecutar el algoritmo. Los primeros dos campos siempre se conservan.
                </p>
            </div>

            <form action="{{ route('algorithm.palindromes') }}" method="POST" data-palindrome-form novalidate>
                @csrf

                @error('words')
                    <p class="field-error mb-4" role="alert">{{ $message }}</p>
                @enderror

                <div class="space-y-4" data-word-fields>
                    @foreach ($currentWords as $index => $word)
                        <div class="word-field-row" data-word-field>
                            <div class="min-w-0">
                                <label for="word-{{ $index }}" class="field-label">Palabra {{ $index + 1 }} <span
                                        class="text-red-700" aria-hidden="true">*</span></label>
                                <input id="word-{{ $index }}" name="words[{{ $index }}]" type="text"
                                    value="{{ $word }}" maxlength="100" autocomplete="off" class="field-input"
                                    data-word-input required>
                                @error("words.$index")
                                    <p class="field-error" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($index >= 2)
                                <button type="button" class="word-remove-button" data-remove-word
                                    aria-label="Eliminar palabra {{ $index + 1 }}">Eliminar</button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <p class="mt-4 text-sm text-gray-600" data-word-status aria-live="polite"></p>
                <p class="field-error mt-1" data-words-client-error role="alert" hidden></p>

                <div class="mt-5 flex flex-wrap gap-3">
                    <button type="button" class="secondary-button" data-add-word>+ Agregar palabra</button>
                    <button type="submit" class="primary-button" data-evaluate-words>Evaluar palabras</button>
                </div>
            </form>
        </section>

        <section class="panel" aria-labelledby="results-heading">
            <div class="panel-heading">
                <p class="panel-kicker">Resultado</p>
                <h2 id="results-heading" class="text-2xl font-semibold tracking-[-0.025em]">Análisis</h2>
            </div>

            @if (!empty($results))
                <div class="space-y-3">
                    @foreach ($results as $result)
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900">{{ $result['word'] }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">Palabra {{ $loop->iteration }}</p>
                            </div>

                            @if ($result['is_palindrome'])
                                <span class="inline-flex shrink-0 items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">Palíndromo</span>
                            @else
                                <span class="inline-flex shrink-0 items-center rounded-full bg-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-700">No palíndromo</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 border-t border-gray-200 pt-4">
                    <p class="text-sm text-gray-600">Se evaluaron <strong class="font-semibold text-gray-900">{{ count($results) }}</strong> palabras.</p>
                    <p class="mt-1 text-sm text-gray-600">Palíndromos encontrados: <strong class="font-semibold text-gray-900">{{ collect($results)->where('is_palindrome', true)->count() }}</strong></p>
                </div>
            @else
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-5 py-8 text-center">
                    <p class="text-sm font-medium text-gray-700">Aún no hay resultados.</p>
                    <p class="mt-1 text-sm leading-6 text-gray-500">Completa al menos tres palabras y presiona <strong>Evaluar palabras</strong>.</p>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
