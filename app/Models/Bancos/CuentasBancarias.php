<?php

namespace App\Models\Bancos;

use App\Models\SociosNegocios\Clientes;
use Illuminate\Database\Eloquent\Model;

class CuentasBancarias extends Model
{

    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'banco_id',
        'numero_cuenta',
        'tipo_cuenta',
        'cliente_id',
        'moneda',
        'estado'
    ];


    public function banco()
    {
        return $this->belongsTo(Bancos::class, 'banco_id');
    }

    public function clientes()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }
}
