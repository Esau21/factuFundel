<?php

namespace App\Models\Ventas;

use App\Models\Producto\Producto;
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
        'observaciones',
        'documento_dte_id',
    ];

    public function clientes()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
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
