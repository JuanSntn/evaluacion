<x-layouts.app title="Editar usuario">
    <div class="mb-7 max-w-2xl">
        <p class="mb-1 text-sm text-gray-500">Usuarios y Servicios / Usuarios</p>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Editar usuario</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">Actualiza la información de {{ $managedUser->name }}.</p>
    </div>

    <section class="panel max-w-3xl" aria-labelledby="user-form-heading">
        <div class="panel-heading">
            <p class="panel-kicker">Datos del usuario</p>
            <h2 id="user-form-heading" class="text-2xl font-semibold tracking-[-0.025em] text-gray-900">Información de registro</h2>
            <p class="mt-1 text-sm text-gray-600"><span class="text-red-700" aria-hidden="true">*</span> Campos obligatorios</p>
        </div>

        <form action="{{ route('users-and-services.users.update', $managedUser) }}" method="POST" data-validate-form novalidate>
            @csrf
            @method('PUT')

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-form.input name="name" label="Nombre completo" autocomplete="name"
                        :value="old('name', $managedUser->name)" required />
                </div>

                <x-form.input name="rfc" label="RFC" autocomplete="off" maxlength="13" class="uppercase"
                    :value="old('rfc', $managedUser->rfc)" data-rfc required />

                <x-form.input name="phone" label="Teléfono" type="tel" autocomplete="tel" inputmode="numeric"
                    maxlength="10" :value="old('phone', $managedUser->phone)" data-phone required />

                <x-form.input name="website" label="Sitio web" type="url" autocomplete="url"
                    :value="old('website', $managedUser->website)" data-website />

                <div class="field-group sm:col-span-2">
                    <label for="address" class="field-label">Dirección <span class="text-red-700" aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span></label>
                    <textarea id="address" name="address" rows="4" autocomplete="street-address"
                        class="field-input resize-y @error('address') border-red-600 @enderror" required
                        @if ($errors->has('address')) aria-invalid="true" @endif
                        aria-describedby="address-client-error address-server-error">{{ old('address', $managedUser->address) }}</textarea>
                    <p id="address-client-error" class="field-error" data-client-error role="alert" hidden></p>
                    @error('address')
                        <p id="address-server-error" class="field-error" role="alert" data-server-error>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('users-and-services.users.show', $managedUser) }}" class="secondary-button">Cancelar</a>
                <button type="submit" class="primary-button">Guardar cambios</button>
            </div>
        </form>
    </section>
</x-layouts.app>
