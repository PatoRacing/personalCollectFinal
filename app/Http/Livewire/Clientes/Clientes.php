<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Deudor;
use App\Models\Cliente;
use Livewire\Component;
use App\Models\Telefono;
use Livewire\WithFileUploads;
use App\Imports\DeudoresImport;
use App\Imports\TelefonoImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class Clientes extends Component
{
    use WithFileUploads;

    //Modales
    public $modalNuevoCliente = false;
    public $modalActualizarCliente = false;
    //Alertas
    public $alertaExito;
    public $alertaError;
    public $alertaImportacion;
    public $mensajeAlerta;
    //Mensajes
    public $mensajeError;
    public $errorEncabezadosContacto;
    public $errorEncabezados;
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeTres;
    public $mensajeCuatro;
    //Variables del formulario
    public $cliente;
    public $nombre;
    public $contacto;
    public $telefono;
    public $nuevo_email;
    public $domicilio;
    public $localidad;
    public $codigo_postal;
    public $provincia;
    //Archivos subidos
    public $archivoSubido;
    public $archivoExcel;

    public function mount()
    {
        if (session()->has('mensajesImportacion')) {
            $mensajes = session('mensajesImportacion');
            $this->mensajeUno = $mensajes[0] ?? '';
            $this->mensajeDos = $mensajes[1] ?? '';
        }
    }

    public function gestiones($contexto, $clienteId = null)
    {
        $this->reset(['nombre', 'contacto', 'telefono', 'nuevo_email', 'domicilio', 'localidad',
                            'codigo_postal', 'provincia', 'archivoSubido'
                    ]);
        $this->resetValidation();
        $this->mensajeUno = '';
        $this->mensajeDos = '';
        $this->mensajeTres = '';
        $this->mensajeCuatro = '';
        $this->alertaExito = false;
        $this->alertaError = false;
        $this->mensajeAlerta = '';
        //Modal nuevo cliente
        if($contexto == 1)
        {
            $this->modalNuevoCliente = true;
        }
        //Cerrar modal nuevo cliente
        elseif($contexto == 2)
        {
            $this->modalNuevoCliente = false;
        }
        //Modal actualizar cliente
        elseif($contexto == 3)
        {
            $this->cliente = Cliente::find($clienteId);
            $this->nombre = $this->cliente->nombre;
            $this->contacto = $this->cliente->contacto;
            $this->telefono = $this->cliente->telefono;
            $this->nuevo_email = $this->cliente->email;
            $this->domicilio = $this->cliente->domicilio;
            $this->localidad = $this->cliente->localidad;
            $this->codigo_postal = $this->cliente->codigo_postal;
            $this->provincia = $this->cliente->provincia;
            $this->modalActualizarCliente = true;
        }
        //Cerrar modal actualizar cliente
        elseif($contexto == 4)
        {
            $this->modalActualizarCliente = false;
        }
        //Limpiar importacion deudores
        elseif($contexto == 5)
        {
            $this->resetValidation();
            $this->reset(['archivoSubido']);
            $this->errorEncabezados = false;
        }
        //Limpiar importacion informacion
        elseif($contexto == 6)
        {
            $this->resetValidation();
            $this->reset(['archivoExcel']);
            $this->errorEncabezadosContacto = false;
        }
    }

    public function nuevoCliente()
    {
        $this->validarCliente();
        $nuevoCliente = new Cliente([
            'nombre' => $this->nombre,
            'contacto' => $this->contacto,
            'telefono' => $this->telefono,
            'email' => $this->nuevo_email,
            'domicilio' => $this->domicilio,
            'localidad' => $this->localidad,
            'codigo_postal' => $this->codigo_postal,
            'provincia' => $this->provincia,
            'estado' => 1,
            'ult_modif' => auth()->id()
        ]);
        $nuevoCliente->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Cliente generado correctamente.";
        $this->alertaExito = true;
        $this->render();
    }

    public function actualizarCliente()
    {
        $this->validarCliente();
        $this->cliente->nombre = $this->nombre;
        $this->cliente->contacto = $this->contacto;
        $this->cliente->telefono = $this->telefono;
        $this->cliente->email = $this->nuevo_email;
        $this->cliente->domicilio = $this->domicilio;
        $this->cliente->localidad = $this->localidad;
        $this->cliente->codigo_postal = $this->codigo_postal;
        $this->cliente->provincia = $this->provincia;
        $this->cliente->ult_modif = auth()->id();
        $this->cliente->save();
        $contexto = 4;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Cliente actualizado correctamente.";
        $this->alertaExito = true;
        $this->render();
    }

    private function validarCliente($actualizar = false)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'contacto' => 'required|string|max:255',
            'telefono' => 'required|string|max:20|regex:/^[0-9]+$/',
            'nuevo_email' => 'required|email|max:255|unique:a_usuarios,email' . ($actualizar ? ',' . $this->usuario->id : ''),
            'domicilio' => 'required|string|max:255',
            'localidad' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'provincia' => 'required|string|max:255',
        ];
        $this->validate($rules);
    }
    
    public function importarDeudores()
    {
        $this->validate([
            'archivoSubido' => 'required|file|mimes:xls,xlsx|max:10240'
        ]);      
        $excel = $this->archivoSubido;
        // Condicion 1: los encabezados deben ser exactamente iguales
        $encabezadosEsperados = ['nombre', 'tipo_doc', 'nro_doc', 'cuil', 'domicilio', 'localidad', 'codigo_postal'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->errorEncabezados = true;
            return; 
        }    
        try
        {
            //Condicion 2: el tipo máximo para importar es de 20 minutos
            ini_set('max_execution_time', 1200);
            DB::beginTransaction();
            $inicioDeImportacion = time();
            $importarDeudores = new DeudoresImport;
            Excel::import($importarDeudores, $excel);
            //Obtengo los resultados de la importacion
            $deudoresImportados = $importarDeudores->procesarDeudoresImportados;
            //Condicion 3: Si no hay nro_doc la instancia se omite
            $deudoresOmitidos = $importarDeudores->deudoresSinDocumento; 
            $contadorNuevosDeudores = 0;
            foreach($deudoresImportados as $deudorImportado)
            {
                //Condicion 4: el tiempo máximo para importar es de 20 minutos
                if (!$this->validarTiempoDeImportacion($inicioDeImportacion)) {
                    return; 
                }
                //Condición 5: solo se guarda un nuevo registro en caso de que el deudor no exista en BD
                $deudorEnBD = Deudor::where('nro_doc', trim((string)$deudorImportado['nro_doc']))->first();
                if(!$deudorEnBD)
                {
                    $nuevoDeudor = new Deudor([
                        'nombre' => ucwords(strtolower(trim($deudorImportado['nombre']))),
                        'tipo_doc' => strtoupper(trim($deudorImportado['tipo_doc'])),
                        'nro_doc' => preg_replace('/\D/', '', $deudorImportado['nro_doc']),
                        'cuil' => preg_replace('/\D/', '', $deudorImportado['cuil']),
                        'domicilio' => ucwords(strtolower(trim($deudorImportado['domicilio']))),
                        'localidad' => ucwords(strtolower(trim($deudorImportado['localidad']))),
                        'codigo_postal' => trim($deudorImportado['codigo_postal']),
                        'ult_modif' => auth()->id(), 
                    ]);
                    $contadorNuevosDeudores++;
                    $nuevoDeudor->save();
                }
                //Si el deudor ya existe se actualiza con la informacion de la importacion
                else
                {
                    $deudorEnBD->nombre = ucwords(strtolower(trim($deudorImportado['nombre'])));
                    $deudorEnBD->tipo_doc = strtoupper(trim($deudorImportado['tipo_doc']));
                    $deudorEnBD->nro_doc = preg_replace('/\D/', '', $deudorImportado['nro_doc']);
                    $deudorEnBD->cuil = preg_replace('/\D/', '', $deudorImportado['cuil']);
                    $deudorEnBD->domicilio = ucwords(strtolower(trim($deudorImportado['domicilio'])));
                    $deudorEnBD->localidad = ucwords(strtolower(trim($deudorImportado['localidad'])));
                    $deudorEnBD->codigo_postal = trim($deudorImportado['codigo_postal']);
                    $deudorEnBD->ult_modif = auth()->id();
                    $deudorEnBD->update();
                }
            }
            DB::commit();
            // Mensaje para deudores omitidos
            $this->mensajeUno = 
                'Importación realizada correctamente (ver resumen en perfil).';
            $this->importacionExitosa();
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            $this->alertaError = true;
            $this->mensajeError = 'Ocurrió un error inesperado durante la importación: ' . $e->getMessage();
            return;
        }
    }

    public function importarInformacion()
    {
        // Condicion 1: Deben haberse importado previamente los deudores
        $this->validate([
            'archivoExcel' => 'required|file|mimes:xls,xlsx|max:10240'
        ]);
        $excel = $this->archivoExcel;
        // Condicion 2: los encabezados deben ser exactamente iguales
        $encabezadosEsperados = ['documento', 'cuil', 'email', 'telefono_uno', 'telefono_dos', 'telefono_tres'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->errorEncabezadosContacto = true;
            return; 
        }
        try
        {
            //Condicion 3: el tipo máximo para importar es de 20 minutos
            ini_set('max_execution_time', 1200);
            DB::beginTransaction();
            $inicioDeImportacion = time();
            $importarInformacion = new TelefonoImport;
            Excel::import($importarInformacion, $excel);
            //Obtengo los resultados de la importacion
            $registrosImportados = $importarInformacion->procesarRegistrosImportados;
            //Condicion 4: Si no hay nro_doc la instancia se omite
            $registrosOmitidos = $importarInformacion->registrosSinDocumento; 
            $nuevosCuils = 0;
            $nuevosMails = 0;
            $nuevosTelefonos = 0;
            foreach($registrosImportados as $registroImportado)
            {
                //Validar el tiempo máximo para importar es de 20 minutos
                if (!$this->validarTiempoDeImportacion($inicioDeImportacion)) {
                    return; 
                }
                //Condicion 5: si existe un deudor para el doc y el mismo no tiene cuil Y si en la importación hay cuil
                //Se actualiza el deudor con el cuil importado
                $deudor = $this->obtenerDeudor($registroImportado, $nuevosCuils);
                if($deudor && $registroImportado['email'])
                {
                    //Condicion 6: si existe deudor para el doc y si en la importacion hay mail.. se crea nuevo registro
                    //Condicion 7: si existe un mail para el deudor pero es distinto al importado.. se crea nuevo registro
                    $mailDeudor = $registroImportado['email'];
                    $this->procesarEmail($deudor, $mailDeudor, $nuevosMails);
                }
                //Condicion 8: si existe deudor para el doc y si en la importacion hay telefono.. se crea nuevo registro
                    //Condicion 9: si existe un telefono para el deudor pero es distinto al importado.. se crea nuevo registro
                $telefonos = [
                    'telefono_uno' => $registroImportado['telefono_uno'],
                    'telefono_dos' => $registroImportado['telefono_dos'],
                    'telefono_tres' => $registroImportado['telefono_tres']
                ];
                foreach ($telefonos as $tipoTelefono => $numero) {
                    if ($deudor && $numero) {
                        $this->procesarTelefono($deudor, $numero, $nuevosTelefonos);
                    }
                }
            }
            DB::commit();
            // Mensaje para registros omitidos
            $this->mensajeUno = $registrosOmitidos == 0
                    ? 'Todos los registros tenían DNI.'
                    : ($registrosOmitidos == 1
                        ? 'Se omitió un registro porque no tiene nro. documento.'
                        : "Se omitieron {$registrosOmitidos} registros porque no tienen nro. documento.");

            // Mensaje para nuevos cuils
            $this->mensajeDos = $nuevosCuils == 0
                    ? 'No se guardaron nuevos cuils porque no habia deudor para el documento o ya existían en la BD.'
                    : ($nuevosCuils == 1
                        ? 'Se guardó el cuil de un deudor.'
                        : "Se guardaron {$nuevosCuils} cuils de {$nuevosCuils} deudores.");
            // Mensaje para nuevos mails
            $this->mensajeTres = $nuevosMails == 0
                    ? 'No se guardaron nuevos mails porque no habia deudor para el documento o ya existían en la BD.'
                    : ($nuevosMails == 1
                        ? 'Se guardó el mail de un deudor.'
                        : "Se guardaron {$nuevosMails} mails de {$nuevosMails} deudores.");
            // Mensaje para nuevos telefonos
            $this->mensajeCuatro = $nuevosTelefonos == 0
                    ? 'No se guardaron nuevos teléfonos porque no habia deudor para el documento o ya existían en la BD.'
                    : ($nuevosTelefonos == 1
                        ? 'Se guardó el teléfono de un deudor.'
                        : "Se guardaron {$nuevosTelefonos} teléfonos de {$nuevosTelefonos} deudores.");

            $this->importacionExitosa();
        } 
        catch(\Exception $e)
        {
            DB::rollBack();
            $this->alertaError = true;
            $this->mensajeError = 'Ocurrió un error inesperado durante la importación: ' . $e->getMessage();
            return;
        }
    }

    private function validarEncabezados($encabezadosEsperados, $excel)
    {
        $encabezadosExcel = (new HeadingRowImport())->toArray($excel)[0][0];
        if ($encabezadosEsperados !== $encabezadosExcel) {
            $this->mensajeError = "Los encabezados del archivo son incorrectos.";
            return false; 
        }
        return true; 
    }

    private function validarTiempoDeImportacion($inicioDeImportacion)
    {
        if (time() - $inicioDeImportacion > 1200) {
            DB::rollBack(); 
            $this->alertaError = true; 
            $this->mensajeError = "Error: La importación ha excedido el tiempo máximo permitido de 20 minutos.";
            return false; // 
        }
        return true; 
    }

    private function importacionExitosa()
    {
        $this->alertaExito = true;
        return redirect()->route('clientes')->with([
            'alertaExito' => true,
            'mensajeUno' => $this->mensajeUno,
            'mensajeDos' => $this->mensajeDos,
            'mensajeTres' => $this->mensajeTres,
            'mensajeCuatro' => $this->mensajeCuatro,
        ]);
    }

    private function obtenerDeudor($registroImportado, &$nuevosCuils)
    {
        $documento = trim((string) $registroImportado['documento']);
        $cuil = preg_replace('/[^0-9]/', '', trim($registroImportado['cuil']));
        $deudor = Deudor::where('nro_doc', $documento)->first();
        if ($deudor && !$deudor->cuil && $cuil)
        {
            $deudor->cuil = $cuil;
            $deudor->ult_modif = auth()->id();
            $deudor->update();
            $nuevosCuils++;
        }
        return $deudor;
    }

    private function procesarEmail ($deudor, $mailDeudor, &$nuevosMails)
    {
        //Busco al deudor y sus posibles mails
        $deudorId = $deudor->id;
        $emailsExistentes = Telefono::where('deudor_id', $deudorId)->pluck('email');
        //Si en la importacion hay mail se crea uno nuevo
        if ($emailsExistentes->isEmpty())
        {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $mailDeudor, 'email');
            $nuevosMails++;
        }
        //Si el deudor tenia mail pero en la importacion hay uno distinto mail se crea uno nuevo
        elseif (!$emailsExistentes->contains($mailDeudor))
        {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $mailDeudor, 'email');
            $nuevosMails++;
        };
    }

    private function procesarTelefono($deudor, $numero, &$nuevosTelefonos)
    {
        $deudorId = $deudor->id;
        $telefonosExistentes = Telefono::where('deudor_id', $deudorId)->pluck('numero');
        if ($telefonosExistentes->isEmpty()) {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $numero, 'numero');
            $nuevosTelefonos++;
        } elseif (!$telefonosExistentes->contains($numero)) {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $numero, 'numero');
            $nuevosTelefonos++;
        }
    }

    private function crearTelefono($deudorId, $tipo, $contacto, $valor, $campo)
    {
        $telefono = new Telefono([
            'deudor_id' => $deudorId,
            'tipo' => $tipo,
            'contacto' => $contacto,
            $campo => $valor, 
            'estado' => 2,
            'ult_modif' => auth()->id(),
        ]);
        $telefono->save();
    }

    public function render()
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.clientes.clientes',[
            'clientes' => $clientes,
        ]);
    }
}
