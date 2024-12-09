<div class="max-h-[28rem]  overflow-y-auto">
    @if($gestiones->isEmpty() && $gestionesMultiproducto->isEmpty())
        @if(session('gestionEliminada'))
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 3000)" 
                x-show="show" 
                class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
                <p>{{ session('mensajeUno') }}</p>
            </div>
        @endif
        <p class="text-center font-bold mt-2">
            Aún hay gestiones realizadas.
        </p>
    @else
        @if(session('nuevaGestion'))
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 3000)" 
                x-show="show" 
                class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{ session('mensajeUno') }}</p>
            </div>
        @endif
        @if($gestiones->isNotEmpty())
            <div>
                <h4 class="{{config('classes.subtituloTres')}} text-regular bg-gray-200">
                    Gestiones sobre la operación
                </h4>
                @foreach ($gestiones as $index => $gestion)
                    <div class="p-2 text-sm border border-gray-400 my-1 md:my-0 lg:my-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}">
                        <p>Monto ofrecido:
                            <span class="font-bold">
                                ${{number_format($gestion->monto_ofrecido, 2, ',', '.')}}
                            </span>
                        </p>
                        <p>Tipo de Propuesta:
                            <span class="font-bold">
                                @if($gestion->tipo_propuesta == 1)
                                    Cancelación
                                @elseif($gestion->tipo_propuesta == 2)
                                    Cuotas fijas
                                @elseif($gestion->tipo_propuesta == 3)
                                    Cuotas Variables
                                @endif
                            </span>
                        </p>
                        @if($gestion->porcentaje_quita)
                            <p>Porcentaje Quita:
                                <span class="font-bold">
                                    {{number_format($gestion->porcentaje_quita, 2, ',', '.')}}%
                                </span>
                            </p>
                        @endif
                        @if($gestion->anticipo)
                            <p>Anticipo:
                                <span class="font-bold">
                                    ${{number_format($gestion->anticipo, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if($gestion->fecha_pago_anticipo)
                            <p>Fecha pago anticipo:
                                <span class="font-bold">
                                    {{ \Carbon\Carbon::parse($gestion->fecha_pago_anticipo)->format('d/m/Y') }}
                                </span>
                            </p>
                        @endif
                        @if($gestion->cantidad_cuotas_uno)
                            <p>Cantidad de Cuotas (1):
                                <span class="font-bold">
                                    {{$gestion->cantidad_cuotas_uno}}
                                </span>
                            </p>
                        @endif
                        @if($gestion->monto_cuotas_uno)
                            <p>Monto cuotas (1):
                                <span class="font-bold">
                                    ${{number_format($gestion->monto_cuotas_uno, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        <p>Fecha de Pago:
                            <span class="font-bold">
                                {{ \Carbon\Carbon::parse($gestion->fecha_pago_cuota)->format('d/m/Y') }}
                            </span>
                        </p>
                        @if($gestion->cantidad_cuotas_dos)
                            <p>Cantidad de Cuotas (2):
                                <span class="font-bold">
                                    {{$gestion->cantidad_cuotas_dos}}
                                </span>
                            </p>
                        @endif
                        @if($gestion->monto_cuotas_dos)
                            <p>Monto cuotas (2):
                                <span class="font-bold">
                                    ${{number_format($gestion->monto_cuotas_dos, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if($gestion->cantidad_cuotas_tres)
                            <p>Cantidad de Cuotas (3):
                                <span class="font-bold">
                                    {{$gestion->cantidad_cuotas_tres}}
                                </span>
                            </p>
                        @endif
                        @if($gestion->monto_cuotas_tres)
                            <p>Monto cuotas (3):
                                <span class="font-bold">
                                    ${{number_format($gestion->monto_cuotas_tres, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if(auth()->user()->rol == 'Administrador')
                            <p>Total ACP:
                                <span class="font-bold">
                                    ${{number_format($gestion->total_acp, 2, ',', '.')}}
                                </span>
                            </p>
                            <p>Honorarios:
                                <span class="font-bold">
                                    ${{number_format($gestion->honorarios, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        <p>Acción:
                            <span class="font-bold">
                                {{$gestion->accion}}
                            </span>
                        </p>
                        <p>Contacto:
                            @if(!$gestion->contacto)
                                <span class="font-bold">
                                    Contacto Eliminado
                                </span>
                            @else
                                <span class="font-bold">
                                    {{$gestion->contacto->numero}}
                                </span>
                            @endif
                        </p>
                        <p>Resultado:
                            <span class="font-bold">
                                @if($gestion->resultado == 1)
                                    Negociación
                                @elseif($gestion->resultado == 2)
                                    Propuesta de Pago
                                @elseif($gestion->resultado == 3)
                                    Archivada
                                @elseif($gestion->resultado == 4)
                                    Acuerdo de Pago
                                @elseif($gestion->resultado == 5)
                                    Rechazada
                                @elseif($gestion->resultado == 6)
                                    Cancelada
                                @elseif($gestion->resultado == 7)
                                    Finalizada
                                @endif
                            </span>
                        </p>
                        <p>Multiproducto:
                            <span class="font-bold">
                                @if($gestion->multiproducto == 1)
                                    Si
                                @else
                                    No
                                @endif
                            </span>
                        </p>
                        @if($gestion->multiproducto == 1)
                            @php
                                $operacionesAbarcadas = \App\Models\GestionOperacion::where('gestion_id', $gestion->id)->get();
                            @endphp
                            <p>Op. abarcadas:
                                <span class="font-bold">
                                    @foreach ($operacionesAbarcadas as $operacionAbarcada)
                                        {{$operacionAbarcada->operacion->operacion}}
                                    @endforeach
                                </span>
                            </p>
                        @endif
                        <p>Observaciones:
                            <span class="font-bold">
                                {{$gestion->observaciones}}
                            </span>
                        </p>
                        <!--solo se permiten acciones en la ultima gestion-->
                        @if($ultimaGestion->id == $gestion->id)
                            <div class="grid {{ $gestion->resultado == 1 ? 'grid-cols-3' : 'grid-cols-1' }}
                                    justify-center gap-1 px-2 mt-1">
                                <!--solo se permiten acciones adicionales si la gestion esta en estado negociacion-->
                                @if($gestion->resultado == 1)
                                    <button type="button" class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                            wire:click="gestiones(1, {{$gestion->id}}, {{$gestion->multiproducto}})">
                                        Confirmar
                                    </button> 
                                    <button type="button" class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                                            wire:click="gestiones(3, {{$gestion->id}}, {{$gestion->multiproducto}})">
                                        Archivar
                                    </button>  
                                @endif          
                                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(5, {{$gestion->id}}, {{$gestion->multiproducto}})">
                                    Eliminar
                                </button>         
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
        @if($gestionesMultiproducto->isNotEmpty())
            <div>
                <h4 class="{{config('classes.subtituloTres')}} text-regular bg-gray-200">
                    Gestiones multiproducto
                </h4>
                @foreach ($gestionesMultiproducto as $index => $gestionMultiproducto)
                    <div class="p-2 border text-sm border-gray-400 my-1 md:my-0 lg:my-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}">
                        <p>Monto ofrecido:
                            <span class="font-bold">
                                ${{number_format($gestionMultiproducto->gestion->monto_ofrecido, 2, ',', '.')}}
                            </span>
                        </p>
                        <p>Tipo de Propuesta:
                            <span class="font-bold">
                                @if($gestionMultiproducto->gestion->tipo_propuesta == 1)
                                    Cancelación
                                @elseif($gestionMultiproducto->gestion->tipo_propuesta == 2)
                                    Cuotas fijas
                                @elseif($gestionMultiproducto->gestion->tipo_propuesta == 3)
                                    Cuotas Variables
                                @endif
                            </span>
                        </p>
                        @if($gestionMultiproducto->gestion->porcentaje_quita)
                            <p>Porcentaje Quita:
                                <span class="font-bold">
                                    {{number_format($gestionMultiproducto->gestion->porcentaje_quita, 2, ',', '.')}}%
                                </span>
                            </p>
                        @endif
                        @if($gestionMultiproducto->gestion->anticipo)
                            <p>Anticipo:
                                <span class="font-bold">
                                    ${{number_format($gestionMultiproducto->gestion->anticipo, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if($gestionMultiproducto->gestion->fecha_pago_anticipo)
                            <p>Fecha pago anticipo:
                                <span class="font-bold">
                                    {{ \Carbon\Carbon::parse($gestionMultiproducto->gestion->fecha_pago_anticipo)->format('d/m/Y') }}
                                </span>
                            </p>
                        @endif
                        @if($gestionMultiproducto->gestion->cantidad_cuotas_uno)
                            <p>Cantidad de Cuotas (1):
                                <span class="font-bold">
                                    {{$gestionMultiproducto->gestion->cantidad_cuotas_uno}}
                                </span>
                            </p>
                        @endif
                        @if($gestionMultiproducto->gestion->monto_cuotas_uno)
                            <p>Monto cuotas (1):
                                <span class="font-bold">
                                    ${{number_format($gestionMultiproducto->gestion->monto_cuotas_uno, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        <p>Fecha de Pago:
                            <span class="font-bold">
                                {{ \Carbon\Carbon::parse($gestionMultiproducto->gestion->fecha_pago_cuota)->format('d/m/Y') }}
                            </span>
                        </p>
                        @if($gestionMultiproducto->gestion->cantidad_cuotas_dos)
                            <p>Cantidad de Cuotas (2):
                                <span class="font-bold">
                                    {{$gestionMultiproducto->gestion->cantidad_cuotas_dos}}
                                </span>
                            </p>
                        @endif
                        @if($gestionMultiproducto->gestion->monto_cuotas_dos)
                            <p>Monto cuotas (2):
                                <span class="font-bold">
                                    ${{number_format($gestionMultiproducto->gestion->monto_cuotas_dos, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if($gestionMultiproducto->gestion->cantidad_cuotas_tres)
                            <p>Cantidad de Cuotas (3):
                                <span class="font-bold">
                                    {{$gestionMultiproducto->gestion->cantidad_cuotas_tres}}
                                </span>
                            </p>
                        @endif
                        @if($gestionMultiproducto->gestion->monto_cuotas_tres)
                            <p>Monto cuotas (3):
                                <span class="font-bold">
                                    ${{number_format($gestionMultiproducto->gestion->monto_cuotas_tres, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if(auth()->user()->rol == 'Administrador')
                            <p>Total ACP:
                                <span class="font-bold">
                                    ${{number_format($gestionMultiproducto->gestion->total_acp, 2, ',', '.')}}
                                </span>
                            </p>
                            <p>Honorarios:
                                <span class="font-bold">
                                    ${{number_format($gestionMultiproducto->gestion->honorarios, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        <p>Acción:
                            <span class="font-bold">
                                {{$gestionMultiproducto->gestion->accion}}
                            </span>
                        </p>
                        <p>Contacto:
                            @if(!$gestionMultiproducto->contacto)
                                <span class="font-bold">
                                    Contacto Eliminado
                                </span>
                            @else
                                <span class="font-bold">
                                    {{$gestionMultiproducto->contacto->numero}}
                                </span>
                            @endif
                        </p>
                        <p>Resultado:
                            <span class="font-bold">
                                @if($gestionMultiproducto->gestion->resultado == 1)
                                    Negociación
                                @elseif($gestionMultiproducto->gestion->resultado == 2)
                                    Propuesta de Pago
                                @elseif($gestionMultiproducto->gestion->resultado == 3)
                                    Archivada
                                @elseif($gestionMultiproducto->gestion->resultado == 4)
                                    Acuerdo de Pago
                                @elseif($gestionMultiproducto->gestion->resultado == 5)
                                    Rechazada
                                @elseif($gestionMultiproducto->gestion->resultado == 6)
                                    Cancelada
                                @elseif($gestionMultiproducto->gestion->resultado == 7)
                                    Finalizada
                                @endif
                            </span>
                        </p>
                        <p>Observaciones:
                            <span class="font-bold">
                                {{$gestionMultiproducto->gestion->observaciones}}
                            </span>
                        </p>
                        <p>Op. negociada:
                            <span class="font-bold">
                                {{$gestionMultiproducto->gestion->operacion->operacion}}
                            </span>
                        </p>
                        <div class="grid grid-cols-1 justify-center gap-1 px-2 mt-1">
                            <a class="{{ config('classes.btn') }} text-center bg-blue-800 hover:bg-blue-900"
                                href="{{ route('operacion.perfil', ['id' => $operacionGestionadaId]) }}">
                                Ver operación gestionada
                            </a>         
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
    @if($modalConfirmarNegociacion)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    Confirmás el procedimiento?
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="confirmarNegociacion">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(2)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalArchivarOperacion)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    Confirmás el procedimiento?
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="archivarGestion">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(4)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalEliminarGestion)
        <x-modal-advertencia>
            <div class="text-sm">
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
            </div>
            @if($contextoModalEliminacion == 1)
                <!-- Botonera -->
                <div class="w-full mt-2 my-1 px-1 grid grid-cols-1 gap-1">
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                            wire:click.prevent="gestiones(6)">
                        Cancelar
                    </button>
                </div>
            @else   
                <!-- Botonera -->
                <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                            wire:click.prevent="eliminarGestion">
                        Confirmar
                    </button>
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                            wire:click.prevent="gestiones(6)">
                        Cancelar
                    </button>
                </div>
            @endif
        </x-modal-advertencia>
    @endif
</div>
