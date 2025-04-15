<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use App\Models\Producto\Producto;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductoController extends Controller
{
    public function index()
    {
        return view('productos.index');
    }

    public function getIndexDataProductos(Request $request)
    {
        if ($request->ajax()) {
            $data = Producto::getProductosData();
            return DataTables::of($data)
                ->addColumn('imagen', function ($data) {
                    return ' <img src="' . $data->imagen . '" alt="no image" class="img-fluid rounded" width="50px;" height="50px;">';
                })
                ->addColumn('codigo', function ($data) {
                    return $data->codigo ?? 'no data';
                })
                ->addColumn('nombre', function ($data) {
                    return $data->nombre ?? 'no data';
                })
                ->addColumn('descripcion', function ($data) {
                    return $data->descripcion ?? 'no data';
                })
                ->addColumn('precio_compra', function ($data) {
                    return number_format($data->precio_compra, 2) ?? '';
                })
                ->addColumn('precio_venta', function ($data) {
                    return number_format($data->precio_venta, 2) ?? '';
                })
                ->addColumn('stock', function ($data) {
                    return $data->stock ?? 'no data';
                })
                ->addColumn('stock_minimo', function ($data) {
                    return $data->stock_minimo ?? 'no data';
                })
                ->addColumn('unidad_medida', function ($data) {
                    return $data->unidad_medida ?? 'no data';
                })
                ->addColumn('marca', function ($data) {
                    return $data->marca ?? 'no data';
                })
                ->addColumn('estado', function ($data) {
                    return $data->estado ?? 'no data';
                })
                ->addColumn('categoria', function ($data) {
                    return $data->categoria ?? 'no data';
                })
                ->addColumn('acciones', function ($data) {
                    return 'acciones';
                })
                ->rawColumns(['acciones', 'estado'])->make(true);
        }
    }
}
