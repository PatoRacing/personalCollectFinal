<?php

namespace App\Imports;

use App\Models\Pago;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PagosProcesadosImport implements ToModel, WithHeadingRow
{
    public $procesarPagosProcesados = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $pagoId = $row['pago_id']; 
        $montoARendir = str_replace('$', '', $row['monto_a_rendir']);
        $this->procesarPagosProcesados[] =
        [
            'pago_id' => $pagoId,
            'monto_a_rendir' => $montoARendir,
        ];
    }
}
