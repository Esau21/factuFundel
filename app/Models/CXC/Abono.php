<?php

namespace App\Models\CXC;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $table = 'abonos';

    protected $fillable = [
        'cuenta_por_cobrar_id',
        'monto',
        'fecha_abono',
        'metodo_pago',
        'observaciones',
    ];

    public function cuenta()
    {
        return $this->belongsTo(CuentasPorCobrar::class, 'cuenta_por_cobrar_id');
    }
}
