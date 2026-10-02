<x-layouts.app title="Colaboradores">
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="max-w-2xl">
            <p class="mb-1 text-sm text-gray-500">Equipo</p>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Colaboradores</h1>
            <p class="mt-2 text-sm leading-6 text-gray-600">Administra los registros de las personas asociadas a tu
                cuenta.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <span class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600">
                <strong class="font-semibold text-gray-900">{{ $collaborators->total() }}</strong> registrados
            </span>
            <a href="{{ route('collaborators.create') }}" class="primary-button">Nuevo colaborador</a>
        </div>
    </div>

    @if (session('status'))
        <div class="status-message mb-6" role="status">{{ session('status') }}</div>
    @endif

    <section class="panel overflow-hidden p-0" aria-label="Lista de colaboradores">
        @if ($collaborators->isEmpty())
            <div class="px-6 py-14 text-center">
                <div class="mx-auto grid size-11 place-items-center rounded-full bg-blue-50 text-lg font-semibold text-blue-700"
                    aria-hidden="true">+</div>
                <h2 class="mt-4 font-semibold text-gray-900">Aún no tienes colaboradores</h2>
                <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-gray-500">Registra la información personal,
                    fiscal y laboral de tu primer colaborador.</p>
                <a href="{{ route('collaborators.create') }}" class="primary-button mt-5">Registrar colaborador</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium">Colaborador</th>
                            <th scope="col" class="px-5 py-3 font-medium">Puesto</th>
                            <th scope="col" class="px-5 py-3 font-medium">Estado</th>
                            <th scope="col" class="px-5 py-3 font-medium">RFC</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($collaborators as $collaborator)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="min-w-0">
                                            <a href="{{ route('collaborators.show', $collaborator) }}"
                                                class="block truncate font-medium text-gray-900 hover:text-blue-700">
                                                {{ $collaborator->nombre }}
                                            </a>
                                            <p class="mt-0.5 truncate text-xs text-gray-500">{{ $collaborator->correo }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $collaborator->puesto }}</td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">{{ $collaborator->state->nombre }}</span>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-600">{{ $collaborator->rfc }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-4">
                                        <a href="{{ route('collaborators.show', $collaborator) }}"
                                            class="text-link">Ver</a>
                                        <a href="{{ route('collaborators.edit', $collaborator) }}"
                                            class="text-link">Editar</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($collaborators->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">{{ $collaborators->links() }}</div>
            @endif
        @endif
    </section>
</x-layouts.app>
