@php
    $editing = isset($collaborator);
@endphp

<div class="space-y-7">
    <fieldset>
        <legend class="sr-only">Información personal y de identificación</legend>
        <div class="mb-4">
            <p class="panel-kicker">Datos personales</p>
            <h2 class="text-lg font-semibold tracking-[-0.02em] text-gray-900">Información de identificación</h2>
            <p class="mt-1 text-sm text-gray-600">Datos de contacto e identificación del colaborador.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-form.input name="nombre" label="Nombre completo" :value="$collaborator->nombre ?? ''" autocomplete="name"
                    required />
            </div>

            <x-form.input name="correo" label="Correo electrónico" type="email" :value="$collaborator->correo ?? ''"
                autocomplete="email" required />

            <x-form.input name="rfc" label="RFC" :value="$collaborator->rfc ?? ''" autocomplete="off" maxlength="13"
                class="uppercase" data-rfc required />

            <x-form.input name="curp" label="CURP" :value="$collaborator->curp ?? ''" autocomplete="off" minlength="18"
                maxlength="18" class="uppercase" data-curp
                data-curp-entities="{{ $states->pluck('clave_curp')->filter()->push('NE')->unique()->implode(',') }}" required />

            <x-form.input name="numero_seguridad_social" label="Número de Seguridad Social"
                :value="$collaborator->numero_seguridad_social ?? ''" inputmode="numeric" maxlength="11" required />
        </div>
    </fieldset>

    <fieldset class="border-t border-gray-200 pt-6">
        <legend class="sr-only">Información fiscal</legend>
        <div class="mb-4">
            <p class="panel-kicker">Datos fiscales</p>
            <h2 class="text-lg font-semibold tracking-[-0.02em] text-gray-900">RFC</h2>
            <p class="mt-1 text-sm text-gray-600">Domicilio y entidad federativa que corresponden al registro.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @php
                $domicilioFiscalError = $errors->first('domicilio_fiscal');
            @endphp
            <div class="field-group sm:col-span-2">
                <label for="domicilio_fiscal" class="field-label">Domicilio fiscal <span class="text-red-700"
                        aria-hidden="true">*</span><span class="sr-only">(obligatorio)</span></label>
                <textarea id="domicilio_fiscal" name="domicilio_fiscal" rows="3" class="field-input resize-y" required
                    @if ($domicilioFiscalError) aria-invalid="true" @endif
                    aria-describedby="domicilio_fiscal-error domicilio_fiscal-client-error">{{ old('domicilio_fiscal', $collaborator->domicilio_fiscal ?? '') }}</textarea>
                @error('domicilio_fiscal')
                    <p id="domicilio_fiscal-error" class="field-error" role="alert" data-server-error>{{ $message }}</p>
                @enderror
                <p id="domicilio_fiscal-client-error" class="field-error" data-client-error role="alert" hidden></p>
            </div>

            @php
                $stateError = $errors->first('state_id');
            @endphp
            <div class="field-group sm:col-span-2">
                <label for="state_id" class="field-label">Estado <span class="text-red-700" aria-hidden="true">*</span><span
                        class="sr-only">(obligatorio)</span></label>
                <select id="state_id" name="state_id" class="field-input" required
                    @if ($stateError) aria-invalid="true" @endif
                    aria-describedby="state_id-error state_id-client-error">
                    <option value="">Selecciona un estado</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}" @selected((string) old('state_id', $collaborator->state_id ?? '') === (string) $state->id)>
                            {{ $state->nombre }} ({{ $state->clave }})
                        </option>
                    @endforeach
                </select>
                @error('state_id')
                    <p id="state_id-error" class="field-error" role="alert" data-server-error>{{ $message }}</p>
                @enderror
                <p id="state_id-client-error" class="field-error" data-client-error role="alert" hidden></p>
            </div>
        </div>
    </fieldset>

    <fieldset class="border-t border-gray-200 pt-6">
        <legend class="sr-only">Información laboral</legend>
        <div class="mb-4">
            <p class="panel-kicker">Datos laborales</p>
            <h2 class="text-lg font-semibold tracking-[-0.02em] text-gray-900">Condiciones de contratación</h2>
            <p class="mt-1 text-sm text-gray-600">Puesto, tipo de contrato y remuneración del colaborador.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-form.input name="fecha_inicio_laboral" label="Fecha de inicio laboral" type="date"
                :value="$editing && $collaborator->fecha_inicio_laboral ? $collaborator->fecha_inicio_laboral->format('Y-m-d') : ''" required />

            <x-form.input name="tipo_contrato" label="Tipo de contrato" :value="$collaborator->tipo_contrato ?? ''" required />

            <x-form.input name="departamento" label="Departamento" :value="$collaborator->departamento ?? ''" required />

            <x-form.input name="puesto" label="Puesto" :value="$collaborator->puesto ?? ''" required />

            <x-form.input name="salario_diario" label="Salario diario" type="number" step="0.01" min="0"
                :value="$collaborator->salario_diario ?? ''" required />

            <x-form.input name="salario" label="Salario mensual" type="number" step="0.01" min="0"
                :value="$collaborator->salario ?? ''" required />
        </div>
    </fieldset>
</div>
