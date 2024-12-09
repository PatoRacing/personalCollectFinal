<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Gestion;
use App\Models\GestionDeudor;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use App\Models\Telefono;
use Livewire\Component;

class OperacionGestion extends Component
{
    //Auxiliaries
    public $operacion;
    public $telefono;
    //Modales
    public $formularioNuevoTelefono = false;
    public $modalActualizarTelefono = false;
    public $modalEliminarTelefono = false;
    //Mensajes
    public $mensajeUno;
    public $mensajeAlerta;
    //Alertas
    public $gestionTelefono = false;
    public $telefonoEliminado = false;
    public $nuevaGestion = false;
    //Variables de telefono
    public $tipo;
    public $contacto;
    public $numero;
    public $email;
    public $estado;

    protected $listeners = ['gestionIngresada' => 'nuevaGestion'];

    public function nuevaGestion($mensajeAlerta)
    {
        $this->nuevaGestion = true;
        $this->mensajeAlerta = $mensajeAlerta;
        $this->render();
    }

    public function gestiones($contexto, $telefonoId = null)
    {
        //Agregar nuevo telefono
        if($contexto == 1)
        {
            $this->formularioNuevoTelefono = true;
        }
        //Cerrar formulario nuevo telefono
        if($contexto == 2)
        {
            $this->resetValidation();
            $this->reset(['tipo', 'contacto', 'numero', 'email', 'estado']);
            $this->formularioNuevoTelefono = false;
        }
        //Modal actualizar telefono
        if($contexto == 3)
        {
            $this->telefono  = Telefono::find($telefonoId);
            $this->tipo = $this->telefono->tipo ?? null;
            $this->contacto = $this->telefono->contacto ?? null;
            $this->numero = $this->telefono->numero ?? null;
            $this->email = $this->telefono->email ?? null;
            $this->estado = $this->telefono->estado;
            $this->modalActualizarTelefono = true;
        }
        //cerrar modal actualizar telefono
        if($contexto == 4)
        {
            $this->resetValidation();
            $this->reset(['tipo', 'contacto', 'numero', 'email', 'estado']);
            $this->modalActualizarTelefono = false;
        }
        //Modal eliminar telefono
        if($contexto == 5)
        {
            $this->mensajeUno = 'Vas a eliminar regisro de contacto.';
            $this->telefono  = Telefono::find($telefonoId);
            $this->modalEliminarTelefono = true;
        }
        //Cerrar modal eliminar telefono
        if($contexto == 6)
        {
            $this->mensajeUno = '';
            $this->modalEliminarTelefono = false;
        }
    }

    public function nuevoTelefono()
    {
        $this->validate([
            'tipo' => 'required',
            'contacto' => 'required',
            'numero' => 'nullable|string|max:20|regex:/^[0-9]+$/|unique:d_telefonos,numero|required_without:email',
            'email' => 'nullable|email|max:255|unique:d_telefonos,email|required_without:numero',
            'estado' => 'required',
        ]);
        $telefono = new Telefono([
            'deudor_id' => $this->operacion->deudor_id,
            'tipo' => $this->tipo,
            'numero' => $this->numero,
            'email' => $this->email,
            'estado' => $this->estado,
            'ult_modif' => auth()->id()
        ]);
        $telefono->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeUno = 'Teléfono actualizado correctamente';
        $this->gestionTelefono = true;
        $this->render();
    }

    public function actualizarTelefono()
    {
        $this->validate([
            'tipo' => 'required',
            'contacto' => 'required',
            'numero' => 'nullable|string|max:20|regex:/^[0-9]+$/|required_without:email',
            'email' => 'nullable|email|max:255|required_without:numero',
            'estado' => 'required',
        ]);
        $this->telefono->tipo = $this->tipo;
        $this->telefono->contacto = $this->contacto;
        $this->telefono->numero = $this->numero;
        $this->telefono->email = $this->email;
        $this->telefono->estado = $this->estado;
        $this->telefono->save();
        $contexto = 4;
        $this->gestiones($contexto);
        $this->mensajeUno = 'Teléfono actualizado correctamente';
        $this->gestionTelefono = true;
        $this->render();
    }

    public function eliminarTelefono()
    {
        $this->telefono->delete();
        $contexto = 6;
        $this->gestiones($contexto);
        $this->mensajeUno = 'Registro eliminado correctamente';
        $this->telefonoEliminado = true;
        $this->render();
    }
    
    public function render()
    {
        $deudorId = $this->operacion->deudor_id;
        $clienteId = $this->operacion->cliente_id;
        $operacionId = $this->operacion->id;
        $operacionesPermitidas = []; 
        //Buscar que existan otras operaciones del deudor con el mismo cliente
        $operacionesDelDeudor = Operacion::where('deudor_id', $deudorId)
                                ->where('cliente_id', $clienteId)
                                ->where('id', '!=', $operacionId)
                                ->get();
        //Si existen otras operaciones realizamos acciones
        foreach($operacionesDelDeudor as $operacionDelDeudor)
        {
            $operacionDelDeudorId = $operacionDelDeudor->id;
            //Buscamos si las operaciones tienen al menos una gestion
            $ultimaGestionDeLaOperacion = Gestion::where('operacion_id', $operacionDelDeudorId)
                                            ->orderBy('created_at', 'desc')
                                            ->first();
            //Si no tienen gestiones la operacion esta permitida para englobar en multiproducto
            if(!$ultimaGestionDeLaOperacion || $ultimaGestionDeLaOperacion->resultado == 3
                || $ultimaGestionDeLaOperacion->resultado == 5 || $ultimaGestionDeLaOperacion->resultado == 6)
            {
                // Buscar última gestión multiproducto de la operación
                $ultimaGestionMultiproducto = GestionOperacion::where('operacion_id', $operacionDelDeudorId)
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                if (!$ultimaGestionMultiproducto || ($ultimaGestionMultiproducto->gestion && $ultimaGestionMultiproducto->gestion->resultado == 3)
                    ||($ultimaGestionMultiproducto->gestion && $ultimaGestionMultiproducto->gestion->resultado == 5)
                    ||($ultimaGestionMultiproducto->gestion && $ultimaGestionMultiproducto->gestion->resultado == 6))
                {
                    $operacionesPermitidas[] = $operacionDelDeudor;
                }
            }
        }
        $sumaDeOperacionesPermitidas = collect($operacionesPermitidas)->sum('deuda_capital');
        
        $telefonos = Telefono::where('deudor_id', $deudorId)
                            ->orderBy('updated_at', 'desc')
                            ->get();
                            
        return view('livewire.gestiones.operacion-gestion',[
            'telefonos' => $telefonos,
            'operacionesPermitidas' => $operacionesPermitidas,
            'operacionesDelDeudor' => $operacionesDelDeudor,
            'sumaDeOperacionesPermitidas' => $sumaDeOperacionesPermitidas
        ]);
    }
}
