<x-layouts.auth title="Crea tu cuenta">
    <p class="mb-5 text-center text-sm leading-6 text-gray-500">
        Completa tus datos para comenzar.
    </p>

    <form action="{{ route('register.store') }}" method="POST" class="space-y-4" data-validate-form novalidate>
        @csrf

        <x-form.input name="name" label="Nombre completo" autocomplete="name" required />

        <x-form.input name="email" label="Correo electrónico" type="email" autocomplete="email" required />

        <x-form.input name="rfc" label="RFC" autocomplete="off"
            hint="12 caracteres para persona moral o 13 para persona física." maxlength="13" required class="uppercase"
            data-rfc />

        <x-form.input name="password" label="Contraseña" type="password" autocomplete="new-password"
            hint="Mínimo 8 caracteres, con letras y números." minlength="8" required data-password-policy />

        <x-form.input name="password_confirmation" label="Confirma tu contraseña" type="password"
            autocomplete="new-password" minlength="8" required data-confirmation-for="password" />

        <button type="submit" class="primary-button w-full">
            Crear cuenta
        </button>
    </form>

    <p class="mt-6 border-t border-gray-200 pt-5 text-center text-sm text-gray-500">
        ¿Ya tienes una cuenta?

        <a href="{{ route('login') }}" class="text-link ml-1">
            Iniciar sesión
        </a>
    </p>
</x-layouts.auth>
