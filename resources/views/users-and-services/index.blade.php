<x-layouts.app title="Usuarios y Servicios">
    <div class="mb-7 max-w-2xl">
        <p class="mb-1 text-sm text-gray-500">
            Administración
        </p>

        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Usuarios y Servicios
        </h1>

        <p class="mt-2 text-sm leading-6 text-gray-600">
            Consulta los usuarios registrados por tu cuenta y administra los servicios disponibles.
        </p>
    </div>

    @if (session('status'))
        <div class="status-message mb-6" role="status">
            {{ session('status') }}
        </div>
    @endif

    <div data-tabs>
        <div class="tab-list" role="tablist" aria-label="Usuarios y servicios">
            <button id="users-tab" type="button" class="tab-button" role="tab" aria-selected="true"
                aria-controls="users-panel" data-tab-target="users-panel">
                Usuarios
            </button>

            <button id="services-tab" type="button" class="tab-button" role="tab" aria-selected="false"
                aria-controls="services-panel" data-tab-target="services-panel">
                Servicios
            </button>
        </div>

        @include('users-and-services.partials.users', ['users' => $users])
        @include('users-and-services.partials.services')
    </div>
</x-layouts.app>
