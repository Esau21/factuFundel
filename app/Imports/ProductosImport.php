<?php

namespace App\Imports;

use App\Models\Producto\Producto;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ProductosImport implements OnEachRow, WithHeadingRow
{
    public $validos = [];
    public $errores = [];

    public function onRow(Row $row)
    {
        $data = $row->toArray();

        $codigo = $data['codigo'] ?? null;
        $nombre = $data['nombre'] ?? null;

        if (!$codigo || !$nombre) {
            $this->errores[] = array_merge($data, ['error' => 'Código o nombre vacío']);
            return;
        }

        $existeCodigo = Producto::where('codigo', $codigo)->exists();
        $existeNombre = Producto::where('nombre', $nombre)->exists();

        if ($existeCodigo || $existeNombre) {
            $this->errores[] = array_merge($data, [
                'error' => 'Producto con código o nombre ya existe'
            ]);            
            return;
        }

        Producto::create([
            'codigo'        => $codigo,
            'nombre'        => $nombre,
            'descripcion'   => $data['descripcion'] ?? '',
            'precio_compra' => is_numeric($data['precio_compra'] ?? null) ? $data['precio_compra'] : 0,
            'precio_venta'  => is_numeric($data['precio_venta'] ?? null) ? $data['precio_venta'] : 0,
            'stock'         => is_numeric($data['stock'] ?? null) ? $data['stock'] : 0,
            'stock_minimo'  => is_numeric($data['stock_minimo'] ?? null) ? $data['stock_minimo'] : 0,
            'unidad_medida' => $data['unidad_medida'] ?? null,
            'marca'         => $data['marca'] ?? null,
            'imagen'        => $data['imagen'] ?? null,
            'estado'        => $data['estado'] ?? 'activo',
            'categoria_id'  => $data['categoria_id'] ?? null,
        ]);        

        $this->validos[] = $data;
    }
}
