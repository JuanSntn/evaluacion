@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'autocomplete' => null,
    'hint' => null,
    'required' => false,
])

@php
    $id = $attributes->get('id', $name);
    $serverError = $errors->first($name);

    $describedBy = collect([$hint ? $id . '-hint' : null, $serverError ? $id . '-error' : null, $id . '-client-error'])
        ->filter()
        ->implode(' ');
@endphp

<div class="field-group">
    <label for="{{ $id }}" class="field-label">
        {{ $label }}

        @if ($required)
            <span class="text-red-700" aria-hidden="true">*</span>
            <span class="sr-only">(obligatorio)</span>
        @endif
    </label>

    @if ($hint)
        <p id="{{ $id }}-hint" class="field-hint">
            {{ $hint }}
        </p>
    @endif

    <div class="field-input-wrapper">
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
            @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif @required($required)
            @if ($serverError) aria-invalid="true" @endif aria-describedby="{{ $describedBy }}"
            {{ $attributes->except('id')->class(['field-input', 'field-input-password' => $type === 'password']) }}>

        @if ($type === 'password')
            <button type="button" class="password-toggle" data-password-toggle aria-controls="{{ $id }}"
                aria-label="Mostrar contraseña" aria-pressed="false" title="Mostrar contraseña">
                <x-icon name="eye" class="password-icon" data-password-icon="show" />
                <x-icon name="eye-off" class="password-icon" data-password-icon="hide" hidden />
            </button>
        @endif
    </div>

    @if ($serverError)
        <p id="{{ $id }}-error" class="field-error" role="alert" data-server-error>
            {{ $serverError }}
        </p>
    @endif

    <p id="{{ $id }}-client-error" class="field-error" data-client-error role="alert" hidden></p>
</div>
