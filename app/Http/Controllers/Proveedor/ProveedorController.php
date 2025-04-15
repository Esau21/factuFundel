<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Proveedor\Proveedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProveedorController extends Controller
{
    public function index()
    {
        return view('proveedores.index');
    }

    public function getIndexDataProveedores(Request $request)
    {
        if ($request->ajax()) {
            $data = Proveedor::getIndexData();
            return DataTables::of($data)
                ->addColumn('nombre', function ($data) {
                    return $data->nombre ?? 'sin data';
                })
                ->addColumn('nrc', function ($data) {
                    return $data->nrc ?? 'sin data';
                })
                ->addColumn('nit', function ($data) {
                    return $data->nit ?? 'sin data';
                })
                ->addColumn('telefono', function ($data) {
                    return $data->telefono ?? 'sin data';
                })
                ->addColumn('correo', function ($data) {
                    return $data->correo ?? 'sin data';
                })
                ->addColumn('direccion', function ($data) {
                    return $data->direccion ?? 'sin data';
                })
                ->addColumn('giro', function ($data) {
                    return $data->giro ?? 'sin data';
                })
                ->addColumn('contacto_nombre', function ($data) {
                    return $data->contacto_nombre ?? 'sin data';
                })
                ->addColumn('contacto_cargo', function ($data) {
                    return $data->contacto_cargo ?? 'sin data';
                })
                ->addColumn('estado', function ($data) {
                    return $data->estado ?? 'sin data';
                })
                ->addColumn('acciones', function ($data) {
                    return  'sin data';
                })
                ->rawColumns(['acciones', 'estado'])->make(true);
        }
    }
}
