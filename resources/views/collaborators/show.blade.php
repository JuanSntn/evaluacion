<x-layouts.app title="Detalle del colaborador">
    <div class="mb-7">
        <p class="mb-1 text-sm text-gray-500">Colaboradores</p>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Detalle del colaborador</h1>
    </div>

    @if (session('status'))
        <div class="status-message mb-6" role="status">{{ session('status') }}</div>
    @endif

    <section class="panel max-w-6xl" aria-labelledby="collaborator-heading">
        <div class="panel-heading">
            <div class="flex min-w-0 items-start gap-3">
                <div class="min-w-0">
                    <p class="panel-kicker">Colaborador</p>
                    <h2 id="collaborator-heading"
                        class="truncate text-2xl font-semibold tracking-[-0.025em] text-gray-900">
                        {{ $collaborator->nombre }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ $collaborator->puesto }} ·
                        {{ $collaborator->departamento }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-7">
            <section aria-labelledby="personal-heading">
                <h3 id="personal-heading" class="text-sm font-semibold text-gray-900">Información personal</h3>
                <dl class="mt-4 grid gap-x-8 gap-y-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Nombre completo</dt>
                        <dd class="mt-1 break-words text-sm text-gray-900">{{ $collaborator->nombre }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Correo electrónico</dt>
                        <dd class="mt-1 break-words text-sm text-gray-900">{{ $collaborator->correo }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">RFC</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $collaborator->rfc }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">CURP</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $collaborator->curp }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Número de Seguridad Social</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900">{{ $collaborator->numero_seguridad_social }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="border-t border-gray-200 pt-6" aria-labelledby="fiscal-heading">
                <h3 id="fiscal-heading" class="text-sm font-semibold text-gray-900">Información fiscal</h3>
                <dl class="mt-4 grid gap-x-8 gap-y-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Estado</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $collaborator->state->nombre }}
                            ({{ $collaborator->state->clave }})</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Domicilio fiscal</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-900">
                            {{ $collaborator->domicilio_fiscal }}</dd>
                    </div>
                </dl>
            </section>

            <section class="border-t border-gray-200 pt-6" aria-labelledby="employment-heading">
                <h3 id="employment-heading" class="text-sm font-semibold text-gray-900">Información laboral</h3>
                <dl class="mt-4 grid gap-x-8 gap-y-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Fecha de inicio laboral</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $collaborator->fecha_inicio_laboral->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Tipo de contrato</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $collaborator->tipo_contrato }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Departamento</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $collaborator->departamento }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Puesto</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $collaborator->puesto }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Salario diario</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">
                            ${{ number_format((float) $collaborator->salario_diario, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Salario mensual</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">
                            ${{ number_format((float) $collaborator->salario, 2) }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <div
            class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('collaborators.index') }}" class="secondary-button">Volver a colaboradores</a>
            <div class="flex flex-col gap-3 sm:flex-row">
                <form action="{{ route('collaborators.destroy', $collaborator) }}" method="POST"
                    onsubmit="return confirm('¿Seguro que deseas eliminar este colaborador?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-700 transition-colors hover:bg-red-50 focus:ring-4 focus:ring-red-100 sm:w-auto">Eliminar
                        colaborador</button>
                </form>
                <a href="{{ route('collaborators.edit', $collaborator) }}" class="primary-button">Editar
                    colaborador</a>
            </div>
        </div>
    </section>
</x-layouts.app>
