<x-layouts.app title="Inicio">
    @if (session('status'))
        <div class="status-message mb-8" role="status">{{ session('status') }}</div>
    @endif

    <section class="max-w-2xl" aria-labelledby="dashboard-heading">
        <p class="mb-1 text-sm text-gray-500">Inicio</p>
        <h1 id="dashboard-heading" class="text-2xl font-bold tracking-tight text-gray-900">Hola, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">Selecciona una opción del menú para administrar tu cuenta.</p>

        <div class="mt-7 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Tu cuenta está lista</h2>
            <p class="mt-1 text-sm leading-6 text-gray-500">Puedes actualizar tus datos personales y tu contraseña desde la configuración.</p>
            <a href="{{ route('account.edit') }}" class="secondary-button mt-4">Ir a mi cuenta</a>
        </div>
    </section>
</x-layouts.app>
