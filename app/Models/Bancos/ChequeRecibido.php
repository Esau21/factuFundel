<?php

namespace App\Models\Bancos;

use App\Models\SociosNegocios\Clientes;
use App\Models\Ventas\Sales;
use Illuminate\Database\Eloquent\Model;

class ChequeRecibido extends Model
{
    protected $table = 'cheque_recibidos';

    protected $fillable = [
        'cliente_id',
        'cuenta_bancaria_id',
        'numero_cheque',
        'monto',
        'fecha_emision',
        'fecha_pago',
        'estado',
        'observaciones',
        'correlativo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function cuenta()
    {
        return $this->belongsTo(CuentasBancarias::class, 'cuenta_bancaria_id');
    }

    public function venta()
    {
        return $this->hasOne(Sales::class, 'cheque_bancario_id');
    }
}
