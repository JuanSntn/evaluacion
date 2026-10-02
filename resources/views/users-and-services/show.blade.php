<x-layouts.app title="Detalle del usuario">
    <div class="mb-7">
        <p class="mb-1 text-sm text-gray-500">Usuarios y Servicios / Usuarios</p>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Detalle del usuario</h1>
    </div>

    @if (session('status'))
        <div class="status-message mb-6" role="status">{{ session('status') }}</div>
    @endif

    <section class="panel max-w-5xl" aria-labelledby="managed-user-heading">
        <div class="panel-heading">
            <div class="flex min-w-0 items-start gap-3">
                <div class="min-w-0">
                    <p class="panel-kicker">Nombre</p>
                    <h2 id="managed-user-heading"
                        class="truncate text-2xl font-semibold tracking-[-0.025em] text-gray-900">
                        {{ $managedUser->name }}</h2>
                </div>
            </div>
        </div>

        <dl class="grid gap-x-8 gap-y-5 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium text-gray-500">Nombre completo</dt>
                <dd class="mt-1 break-words text-sm text-gray-900">{{ $managedUser->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">RFC</dt>
                <dd class="mt-1 font-mono text-sm text-gray-900">{{ $managedUser->rfc }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Teléfono</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $managedUser->phone }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">Sitio web</dt>
                <dd class="mt-1 break-words text-sm text-gray-900">
                    @if ($managedUser->website)
                        <a href="{{ $managedUser->website }}" target="_blank" rel="noopener noreferrer"
                            class="text-link">{{ $managedUser->website }}</a>
                    @else
                        <span class="text-gray-500">No registrado</span>
                    @endif
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-gray-500">Dirección</dt>
                <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-900">{{ $managedUser->address }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500">
                    Creado por
                </dt>

                <dd class="mt-1 text-sm text-gray-900">
                    #{{ $managedUser->creator->id }}
                    ·
                    {{ $managedUser->creator->name }}
                </dd>
            </div>
        </dl>

        <div
            class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('users-and-services.index') }}" class="secondary-button">Volver a usuarios</a>
            <div class="flex flex-col gap-3 sm:flex-row">
                <form action="{{ route('users-and-services.users.destroy', $managedUser) }}" method="POST"
                    onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50 focus:ring-4 focus:ring-red-100 sm:w-auto">Eliminar
                        usuario</button>
                </form>
                <a href="{{ route('users-and-services.users.edit', $managedUser) }}" class="primary-button">Editar
                    usuario</a>
            </div>
        </div>
    </section>
</x-layouts.app>
