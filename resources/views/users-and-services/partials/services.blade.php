<section id="services-panel" class="pt-6" role="tabpanel" aria-labelledby="services-tab" data-services
    data-index-url="{{ route('users-and-services.services.posts.index') }}"
    data-store-url="{{ route('users-and-services.services.posts.store') }}"
    data-update-url="{{ route('users-and-services.services.posts.update', ['post' => '__POST_ID__']) }}"
    data-destroy-url="{{ route('users-and-services.services.posts.destroy', ['post' => '__POST_ID__']) }}"
    data-csrf="{{ csrf_token() }}" hidden>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                Servicios
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Consulta y administra las publicaciones obtenidas desde JSONPlaceholder.
            </p>
        </div>

        <button type="button" class="primary-button" data-service-create>
            Nueva publicación
        </button>
    </div>

    <section class="panel mb-6" data-service-form-panel aria-labelledby="service-form-heading" hidden>
        <div class="panel-heading">
            <p class="panel-kicker">
                Publicación
            </p>

            <h3 id="service-form-heading" class="text-xl font-semibold tracking-tight text-gray-900"
                data-service-form-title>
                Nueva publicación
            </h3>

            <p class="mt-1 text-sm text-gray-600">
                Completa el título y contenido de la publicación.
            </p>
        </div>

        <form data-service-form novalidate>
            <input type="hidden" data-service-post-id>

            <div class="grid gap-5">
                <div class="field-group">
                    <label for="service-title" class="field-label">
                        Título

                        <span class="text-red-700" aria-hidden="true">
                            *
                        </span>
                    </label>

                    <input id="service-title" name="title" type="text" maxlength="255"
                        class="field-input" autocomplete="off" data-service-title required>

                    <p class="field-error" data-service-error="title" role="alert" hidden></p>
                </div>

                <div class="field-group">
                    <label for="service-body" class="field-label">
                        Contenido

                        <span class="text-red-700" aria-hidden="true">
                            *
                        </span>
                    </label>

                    <textarea id="service-body" name="body" rows="5" class="field-input resize-y" data-service-body required></textarea>

                    <p class="field-error" data-service-error="body" role="alert" hidden></p>
                </div>
            </div>

            <p class="status-message mt-5" data-service-form-status role="status" hidden></p>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end">
                <button type="button" class="secondary-button" data-service-cancel>
                    Cancelar
                </button>

                <button type="submit" class="primary-button" data-service-submit>
                    Guardar publicación
                </button>
            </div>
        </form>
    </section>

    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-4" data-service-load-error
        role="alert" hidden>
        <p class="text-sm font-medium text-red-800" data-service-load-error-message>
            No fue posible cargar las publicaciones.
        </p>

        <button type="button" class="mt-3 text-sm font-medium text-red-700 underline hover:text-red-900"
            data-service-retry>
            Reintentar
        </button>
    </div>

    <section class="panel overflow-hidden p-0" aria-label="Lista de publicaciones">
        <div class="px-6 py-14 text-center" data-service-loading>
            <p class="font-medium text-gray-900">
                Cargando publicaciones...
            </p>

            <p class="mt-1 text-sm text-gray-500">
                Estamos consultando el servicio.
            </p>
        </div>

        <div class="px-6 py-14 text-center" data-service-empty hidden>
            <h3 class="font-semibold text-gray-900">
                No hay publicaciones disponibles
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Puedes agregar una nueva publicación.
            </p>
        </div>

        <div class="divide-y divide-gray-200" data-service-posts hidden></div>

        <div class="flex flex-col gap-3 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            data-service-pagination hidden>
            <p class="text-sm text-gray-500" data-service-page-info></p>

            <div class="flex items-center gap-2">
                <button type="button" class="secondary-button" data-service-previous>
                    Anterior
                </button>

                <button type="button" class="secondary-button" data-service-next>
                    Siguiente
                </button>
            </div>
        </div>
    </section>

    <template data-service-post-template>
        <article class="px-5 py-5" data-service-post>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-400" data-service-post-number></span>

                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700"
                            data-service-local-label hidden>
                            Nueva
                        </span>
                    </div>

                    <h3 class="font-semibold text-gray-900" data-service-post-title></h3>

                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600" data-service-post-body>
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    <button type="button" class="text-link" data-service-edit>
                        Editar
                    </button>

                    <button type="button" class="text-sm font-medium text-red-700 hover:text-red-800"
                        data-service-delete>
                        Eliminar
                    </button>
                </div>
            </div>
        </article>
    </template>
</section>
