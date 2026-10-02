<x-layouts.auth title="Recupera tu acceso">
    <p class="mb-5 text-center text-sm leading-6 text-gray-500">
        Escribe el correo y RFC exactos de tu registro.
    </p>

    <form action="{{ route('password.recover') }}" method="POST" class="space-y-4" data-validate-form novalidate>
        @csrf

        <x-form.input name="email" label="Correo electrónico" type="email" autocomplete="email" required />

        <x-form.input name="rfc" label="RFC" autocomplete="off"
            hint="12 caracteres para persona moral o 13 para persona física." maxlength="13" required class="uppercase"
            data-rfc />

        <x-form.input name="password" label="Nueva contraseña" type="password" autocomplete="new-password"
            hint="Mínimo 8 caracteres, con letras y números." minlength="8" required data-password-policy />

        <x-form.input name="password_confirmation" label="Confirma la nueva contraseña" type="password"
            autocomplete="new-password" minlength="8" required data-confirmation-for="password" />

        <button type="submit" class="primary-button w-full">
            Actualizar contraseña
        </button>

        <a href="{{ route('login') }}" class="text-link block text-center">
            Volver al inicio de sesión
        </a>
    </form>
</x-layouts.auth>
