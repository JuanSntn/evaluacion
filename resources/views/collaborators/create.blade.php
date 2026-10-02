<x-layouts.app title="Nuevo colaborador">
    <div class="mb-7 max-w-3xl">
        <p class="mb-1 text-sm text-gray-500">Colaboradores</p>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Nuevo colaborador</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">Registra la información personal, fiscal y laboral del colaborador.</p>
    </div>

    <section class="panel max-w-5xl" aria-labelledby="collaborator-create-heading">
        <div class="panel-heading">
            <p class="panel-kicker">Registro de colaborador</p>
            <h2 id="collaborator-create-heading" class="text-2xl font-semibold tracking-[-0.025em] text-gray-900">Información del colaborador</h2>
            <p class="mt-1 text-sm text-gray-600"><span class="text-red-700" aria-hidden="true">*</span> Campos obligatorios</p>
        </div>

        <form action="{{ route('collaborators.store') }}" method="POST" data-validate-form novalidate>
            @csrf
            @include('collaborators._form')

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('collaborators.index') }}" class="secondary-button">Cancelar</a>
                <button type="submit" class="primary-button">Guardar colaborador</button>
            </div>
        </form>
    </section>
</x-layouts.app>
