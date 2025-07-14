<?php

namespace App\Models\CXC;

use App\Models\Ventas\Sales;
use Illuminate\Database\Eloquent\Model;

class CuentasPorCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';

    protected $fillable = [
        'sale_id',
        'monto',
        'fecha_pago',
        'saldo_pendiente',
        'metodo_pago'
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    public static function getCxC()
    {
        $data = CuentasPorCobrar::select('*')->orderBy('id', 'desc')->get();

        return $data;
    }
}
