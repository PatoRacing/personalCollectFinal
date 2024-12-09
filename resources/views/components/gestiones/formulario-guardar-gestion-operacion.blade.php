@props([
    'contexto', 'telefonos', 'operacionesPermitidas', 'anticipo_cuotas_fijas', 'anticipo_cuotas_variables',
    'observaciones', 'multiproducto'
])

<h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
    Confirmar propuesta
</h4>
<!--Accion realizada-->
<div class="m-2">
    <x-input-label for="accion" :value="__('Acción realizada')" />
    <select
        id="accion"
        class="block mt-1 w-full rounded-md border-gray-300"
        wire:model="accion"
    >
        <option value="">Seleccionar</option>
        <option value="Llamada Entrante TP (Fijo)">Llamada Entrante TP (Fijo)</option>
        <option value="Llamada Saliente TP (Fijo)">Llamada Saliente TP (Fijo)</option>
        <option value="Llamada Entrante TP (Celular)">Llamada Entrante TP (Celular)</option>
        <option value="Llamada Saliente TP (Celular)">Llamada Saliente TP (Celular)</option>
        <option value="Llamada Entrante WP (Celular)">Llamada Entrante WP (Celular)</option>
        <option value="Llamada Saliente WP (Celular)">Llamada Saliente WP (Celular)</option>
        <option value="Chat WP (Celular)">Chat WP (Celular)</option>
        <option value="Mensaje SMS (Celular)">Mensaje SMS (Celular)</option>   

    </select>
    <x-input-error :messages="$errors->get('accion')" class="mt-2" />
</div>
<!--Contacto-->
<div class="m-2">
    <x-input-label for="contacto" :value="__('Nro. contacto')" />
    <select
        id="contacto"
        class="block mt-1 w-full rounded-md border-gray-300"
        wire:model="contacto"
    >
        <option value="">Seleccionar</option>
        @foreach ($telefonos as $telefono)
            @if($telefono->numero)
                <option value="{{$telefono->id}}">
                    {{$telefono->numero}}
                </option>
            @endif
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
</div>
<!--Si es cuotas fijas o cuotas variables-->
@if($contexto == 2)
    <!--Si tiene anticipo-->
    @if($anticipo_cuotas_fijas  > 0)
        <div class="m-2">
            <x-input-label for="fecha_pago_anticipo" :value="__('Fecha de pago anticipo:')" />
                <x-text-input
                    id="fecha_pago_anticipo"
                    class="block mt-1 w-full"
                    type="date"
                    wire:model="fecha_pago_anticipo"
                    :value="old('fecha_pago_anticipo')"
                    min="{{ now()->toDateString() }}"
                />
            <x-input-error :messages="$errors->get('fecha_pago_anticipo')" class="mt-2" />
        </div>
    @endif
@endif
@if($contexto == 3)
    <!--Si tiene anticipo-->
    @if($anticipo_cuotas_variables  > 0)
        <div class="m-2">
            <x-input-label for="fecha_pago_anticipo" :value="__('Fecha de pago anticipo:')" />
                <x-text-input
                    id="fecha_pago_anticipo"
                    class="block mt-1 w-full"
                    type="date"
                    wire:model="fecha_pago_anticipo"
                    :value="old('fecha_pago_anticipo')"
                    min="{{ now()->toDateString() }}"
                />
            <x-input-error :messages="$errors->get('fecha_pago_anticipo')" class="mt-2" />
        </div>
    @endif
@endif
<!--Fecha de Pago-->
<div class="m-2">
    @if($contexto == 1)
        <x-input-label for="fecha_de_pago" :value="__('Fecha de pago:')" />
    @else
        <x-input-label for="fecha_de_pago" :value="__('Fecha de pago cuota:')" />
    @endif
        <x-text-input
            id="fecha_de_pago"
            class="block mt-1 w-full"
            type="date"
            wire:model="fecha_de_pago"
            :value="old('fecha_de_pago')"
            min="{{ now()->toDateString() }}"
        />
    <x-input-error :messages="$errors->get('fecha_de_pago')" class="mt-2" />
</div>
@if(!empty($operacionesPermitidas))
    <!--Multproducto-->
    <div class="m-2">
        <x-input-label for="multiproducto" :value="__('Abarca otras operaciones del deudor?')" />
        <select
            id="multiproducto"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="multiproducto"
        >
            <option value="">Seleccionar</option>
            <option value="1">Sí</option>
            <option value="2">No</option>
        </select>
        <x-input-error :messages="$errors->get('multiproducto')" class="mt-2" />
    </div>
    @if($multiproducto == 1)
        <div class="m-2">
            <x-input-label for="operaciones_multiproducto_id" :value="__('Seleccionar operaciones')" />
            <select
                id="operaciones_multiproducto_id"
                class="block mt-1 w-full rounded-md border-gray-300"
                wire:model="operaciones_multiproducto_id"
                multiple
            >
                @foreach ($operacionesPermitidas as $operacionPermitida)
                    <option value="{{$operacionPermitida['id']}}">{{$operacionPermitida['operacion']}}</option>
                @endforeach
            </select>
        </div>
        <x-input-error :messages="$errors->get('operaciones_multiproducto_id')" class="mt-2" />
    @endif
@endif
@if(auth()->user()->rol == 'Administrador')
    <!--Resultado-->
    <div class="m-2">
        <x-input-label for="resultado" :value="__('Resultado:')" />
        <select
            id="resultado"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="resultado"
        >
            <option value="">Seleccionar</option>
            <option value="2">Propuesta de Pago</option>
            <option value="4">Acuerdo de Pago</option>
        </select>
        <x-input-error :messages="$errors->get('resultado')" class="mt-2" />
    </div>
@endif
<!-- Observacion -->
<div class="m-2">
    <x-input-label for="observaciones" :value="__('Observaciones')" />
    <textarea
        id="observaciones"
        placeholder="Describe brevemente la acción"
        class="block mt-1 w-full h-20 rounded-md border-gray-300"
        wire:model="observaciones"
        maxlength="255"
    >{{ old('observaciones') }}</textarea>
    <div class="my-1 text-sm text-gray-500">
        Caracteres restantes: {{ 255 - strlen($observaciones) }}
    </div>
    <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
</div>
