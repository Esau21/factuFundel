<?php

namespace App\Models\Ventas;

use App\Models\Producto\Producto;
use Illuminate\Database\Eloquent\Model;

class SalesDetails extends Model
{
    protected $table = 'sales_details';

    protected $fillable = [
        'sale_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'cambio',
        'sub_total',
        'descuento_porcentaje',
        'descuento_en_dolar'
    ];

    public function sales()
    {
        return $this->belongsTo(Sales::class, 'sale_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
