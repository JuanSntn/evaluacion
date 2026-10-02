<section id="users-panel" class="pt-6" role="tabpanel" aria-labelledby="users-tab">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                Usuarios
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Registra y administra los usuarios creados desde esta cuenta.
            </p>
        </div>

        <a href="{{ route('users-and-services.users.create') }}" class="primary-button">
            Agregar usuario
        </a>
    </div>

    <section class="panel overflow-hidden p-0" aria-label="Lista de usuarios">
        @if ($users->isEmpty())
            <div class="px-6 py-14 text-center">
                <div class="mx-auto grid size-11 place-items-center rounded-full bg-blue-50 text-blue-700"
                    aria-hidden="true">
                    <x-icon name="user" />
                </div>

                <h3 class="mt-4 font-semibold text-gray-900">
                    Aún no tienes usuarios registrados
                </h3>

                <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-gray-500">
                    Agrega el primer usuario que administrarás desde tu cuenta.
                </p>

                <a href="{{ route('users-and-services.users.create') }}" class="primary-button mt-5">
                    Agregar usuario
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">
                                Nombre
                            </th>

                            <th class="px-5 py-3 font-medium">
                                RFC
                            </th>

                            <th class="px-5 py-3 font-medium">
                                Teléfono
                            </th>

                            <th class="px-5 py-3 font-medium">
                                Sitio web
                            </th>

                            <th class="px-5 py-3 text-right font-medium">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 font-medium text-gray-900">
                                    {{ $user->name }}
                                </td>

                                <td class="px-5 py-4 text-gray-600">
                                    {{ $user->rfc }}
                                </td>

                                <td class="px-5 py-4 text-gray-600">
                                    {{ $user->phone }}
                                </td>

                                <td class="px-5 py-4 text-gray-600">
                                    @if ($user->website)
                                        <a href="{{ $user->website }}" target="_blank" rel="noopener noreferrer"
                                            class="text-link">
                                            {{ $user->website }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">
                                            —
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('users-and-services.users.show', $user) }}"
                                            class="text-link">
                                            Ver
                                        </a>

                                        <a href="{{ route('users-and-services.users.edit', $user) }}"
                                            class="text-link">
                                            Editar
                                        </a>

                                        <form action="{{ route('users-and-services.users.destroy', $user) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="text-sm font-medium text-red-700 hover:text-red-800">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </section>
</section>
