<?php

namespace App\Models\Producto;

use App\Models\Categoria\Categoria;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{

    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'unidad_medida',
        'marca',
        'imagen',
        'estado',
        'categoria_id'
    ];

    public function categorias()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public static function getProductosData()
    {
        $data = Producto::leftJoin('categorias as c', 'c.id', '=', 'productos.categoria_id')
            ->select('productos.*', 'c.categoria_nombre as categoria')
            ->orderBy('productos.id', 'desc')
            ->get();

        return $data;
    }

    public function getImagenAttribute($value)
    {
        $imagen = $value ?? $this->attributes['imagen'] ?? null;

        if ($imagen && file_exists(public_path('storage/' . $imagen))) {
            return asset('storage/' . $imagen);
        } else {
            return asset('img/camara1.png');
        }
    }
}
