<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acuerdo extends Model
{
    use HasFactory;

    protected $table = 'k_acuerdos';

    protected $fillable = [
        'gestion_id',
        'pdf_acuerdo',
        'estado',
        //1-Preaprobado: acuerdo sin aprobacion del cliente
        //2-Vigente
        //3-Completo
        //4-Finalizado
        //5-Rendido a cuenta
        //6-Anulado: desde Personal Collect
        //7-Cancelado: desde el Cliente
        'ult_modif',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
