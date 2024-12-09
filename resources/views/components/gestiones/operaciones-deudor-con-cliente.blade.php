@props([
    'operacionesPermitidas', 'operacionesDelDeudor', 'sumaDeOperacionesPermitidas'
])
<div class="max-h-[28rem]  overflow-y-auto">
    @if(!empty($operacionesPermitidas))
        <h4 class="{{config('classes.subtituloTres')}} bg-orange-500 text-white">
            Suma de Operaciones: ${{number_format($sumaDeOperacionesPermitidas, 2, ',', '.')}}
        </h4>
        @foreach ($operacionesPermitidas as $index => $operacionPermitida)
            <div class="px-1 text-sm border border-gray-400 mt-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}"> 
                <a class="{{config('classes.subtituloTres')}} bg-blue-800 hover:bg-blue-900 text-white block"
                        href="{{ route('operacion.perfil', ['id' => $operacionPermitida->id]) }}">
                    Operación {{$operacionPermitida->operacion}}
                </a>
                <div class="p-1">
                    <p>Producto:
                        <span class="font-bold">
                            {{ $operacionPermitida->producto->nombre }}
                        </span>
                    </p>
                    <p>Subproducto:
                        <span class="font-bold">
                            @if($operacionPermitida->subproducto)
                                {{$operacionPermitida->subproducto}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Segmento:
                        <span class="font-bold">
                            {{ $operacionPermitida->segmento }}
                        </span>
                    </p>
                    <p>Deuda Capital:
                        <span class="font-bold">
                            ${{number_format($operacionPermitida->deuda_capital, 2, ',', '.')}}
                        </span>
                    </p>
                    <p>Fecha Asig:
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($operacionPermitida->fecha_asignacion)->format('d/m/Y') }}
                        </span>
                    </p>
                </div>
            </div>
        @endforeach
    @else
        @if($operacionesDelDeudor->count())
            <div class="p-1">
                <p class="text-center font-bold mt-2">
                    Otras operaciones con gestión activa.
                </p>
            </div>
        @else
            <div class="p-1">
                <p class="text-center font-bold mt-2">
                    No hay otras operaciones con el cliente.
                </p>
            </div>
        @endif
    @endif
</div>