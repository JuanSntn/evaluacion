<x-layouts.auth title="Bienvenido de vuelta">
    <p class="mb-5 text-center text-sm leading-6 text-gray-500">
        Ingresa con el correo y la contraseña de tu registro.
    </p>

    @if (session('status'))
        <div class="status-message mb-7" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST" class="space-y-4" data-validate-form novalidate>
        @csrf

        <x-form.input name="email" label="Correo electrónico" type="email" autocomplete="email" required />

        <x-form.input name="password" label="Contraseña" type="password" autocomplete="current-password" required />

        <div class="flex items-center justify-between gap-4 pt-1">
            <a href="{{ route('password.forgot') }}" class="text-link">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

        <button type="submit" class="primary-button w-full">
            Iniciar sesión
        </button>
    </form>

    <p class="mt-6 border-t border-gray-200 pt-5 text-center text-sm text-gray-500">
        ¿Aún no tienes cuenta?

        <a href="{{ route('register') }}" class="text-link ml-1">
            Crear una cuenta
        </a>
    </p>
</x-layouts.auth>
