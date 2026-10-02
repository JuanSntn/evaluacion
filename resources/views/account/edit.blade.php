<x-layouts.app title="Configuración de Cuenta">
    <div class="mb-7 max-w-2xl">
        <p class="mb-1 text-sm text-gray-500">Configuración</p>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">Configuración de Cuenta</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">Actualiza tus datos o cambia tu contraseña.</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.35fr_0.85fr] lg:items-start">
        <section class="panel" aria-labelledby="profile-heading">
            <div class="panel-heading">
                <p class="panel-kicker">Datos fiscales</p>
                <h2 id="profile-heading" class="text-2xl font-semibold tracking-[-0.025em]">Información de la cuenta
                </h2>
            </div>
            @if (session('status'))
                <div class="status-message mb-7" role="status">{{ session('status') }}</div>
            @endif
            <form action="{{ route('account.update') }}" method="POST" class="grid gap-4 sm:grid-cols-2"
                data-validate-form novalidate>
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
                    <x-form.input name="name" label="Nombre completo" :value="$user->name" autocomplete="name"
                        required />
                </div>
                <x-form.input name="email" label="Correo electrónico" type="email" :value="$user->email"
                    autocomplete="email" required />
                <x-form.input name="rfc" label="RFC" :value="$user->rfc" autocomplete="off" maxlength="13" required
                    class="uppercase" data-rfc />
                <x-form.input name="phone" label="Teléfono" type="tel" :value="$user->phone" autocomplete="tel" />
                <x-form.input name="website" label="Sitio web" type="url" :value="$user->website" autocomplete="url"
                    data-website />
                <div class="field-group sm:col-span-2">
                    <label for="address" class="field-label">Dirección</label>
                    <textarea id="address" name="address" rows="4" autocomplete="street-address" class="field-input resize-y">{{ old('address', $user->address) }}</textarea>
                    @error('address')
                        <p class="field-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="primary-button">Guardar cambios</button>
                </div>
            </form>
        </section>

        <section class="panel" aria-labelledby="password-heading">
            <div class="panel-heading">
                <p class="panel-kicker">Seguridad</p>
                <h2 id="password-heading" class="text-2xl font-semibold tracking-[-0.025em]">Cambiar contraseña</h2>
            </div>
            @if (session('password_status'))
                <div class="status-message mb-7" role="status">{{ session('password_status') }}</div>
            @endif
            <form action="{{ route('account.password.update') }}" method="POST" class="space-y-4" data-validate-form
                novalidate>
                @csrf
                @method('PUT')
                <x-form.input name="current_password" label="Contraseña actual" type="password"
                    autocomplete="current-password" required />
                <x-form.input name="password" label="Nueva contraseña" type="password" autocomplete="new-password"
                    hint="Mínimo 8 caracteres, con letras y números." minlength="8" required data-password-policy />
                <x-form.input name="password_confirmation" label="Confirma la nueva contraseña" type="password"
                    autocomplete="new-password" required data-confirmation-for="password" />
                <button type="submit" class="primary-button w-full justify-center">Actualizar contraseña</button>
            </form>
        </section>
    </div>
</x-layouts.app>
