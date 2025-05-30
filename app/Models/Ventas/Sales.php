<?php

namespace App\Models\Ventas;

use App\Models\Bancos\ChequeRecibido;
use App\Models\Bancos\CuentasBancarias;
use App\Models\DGII\DocumentosDte;
use App\Models\SociosNegocios\Clientes;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table = 'sales';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha_venta',
        'total',
        'status',
        'tipo_pago',
        'tipo_venta',
        'plazos',
        'tipo_plazo',
        'abono',
        'saldo_pendiente',
        'observaciones',
        'cambio',
        'documento_dte_id',
        'cheque_bancario_id',
        'cuenta_bancaria_id',
        'monto_efectivo',
        'monto_transferencia',
        'documento_dte_id'
    ];

    public function clientes()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function documentoDte()
    {
        return $this->belongsTo(DocumentosDte::class, 'documento_dte_id');
    }


    public function cheque()
    {
        return $this->belongsTo(ChequeRecibido::class, 'cheque_bancario_id');
    }

    public function cuenta()
    {
        return $this->belongsTo(CuentasBancarias::class, 'cuenta_bancaria_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(SalesDetails::class, 'sale_id');
    }

    public static function getSalesdelDia()
    {
        $data = Sales::select('sales.*')
            ->whereDate('sales.fecha_venta', now()->toDateString())
            ->get();

        return $data;
    }
}
