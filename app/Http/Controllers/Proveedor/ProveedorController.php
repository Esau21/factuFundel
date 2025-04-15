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
                    $editar = '';
                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeleteProveedor({$data->id}); return false;";

                    $editar = '<a href="#" 
                                    class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-proveedor"
                                    data-bs-toggle="modal"
                                    data-bs-target="#EditProveedor"
                                    data-id="' . $data->id . '"
                                    data-nombre="' . e($data->nombre) . '"
                                    data-nrc="' . e($data->nrc) . '"
                                    data-nit="' . $data->nit . '"
                                    data-telefono="' . $data->telefono . '"
                                    data-correo="' . $data->correo . '"
                                    data-direccion="' . $data->direccion . '"
                                    data-notas="' . $data->notas . '"
                                    data-giro="' . $data->giro . '"
                                    data-contacto_nombre="' . $data->contacto_nombre . '"
                                    data-contacto_cargo="' . $data->contacto_cargo . '"
                                    data-estado="' . $data->estado . '"
                                    title="Editar">
                                    <i class="bx bx-edit"></i>
                             </a>';


                    $eliminar = '<a title="Eliminar" class="btn btn-danger mt-mobile mx-2" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                                    <i class="bx bx-trash-alt"></i>
                                 </a>';


                    return $editar . $eliminar;
                })
                ->rawColumns(['acciones', 'estado'])->make(true);
        }
    }


    public function storeProveedor(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'nrc' => 'required',
            'nit' => 'required',
            'telefono' => ['required', 'regex:/^[0-9]+$/'],
            'correo' => 'required|email',
            'direccion' => 'required',
            'notas' => 'required',
            'giro' => 'required',
            'contacto_nombre' => 'required',
            'contacto_cargo' => 'required',
            'estado' => 'required',
        ], [
            'nombre.required' => 'El nombre del proveedor es requerido',
            'nrc.required' => 'El NRC del proveedor es requerido',
            'nit.required' => 'El NIT del proveedor es requerido',
            'telefono.required' => 'El teléfono del proveedor es requerido',
            'telefono.regex' => 'El teléfono solo debe contener números',
            'correo.required' => 'El correo del proveedor es requerido',
            'correo.email' => 'Debe ingresar un correo válido',
            'direccion.required' => 'La dirección del proveedor es requerida',
            'notas.required' => 'La dirección del proveedor es requerida',
            'giro.required' => 'El giro del proveedor es requerido',
            'contacto_nombre.required' => 'El nombre del contacto es requerido',
            'contacto_cargo.required' => 'El cargo del contacto es requerido',
            'estado.required' => 'El estado del proveedor es requerido',
        ]);

        try {
            Proveedor::create([
                'nombre' => $request->nombre,
                'nrc' => $request->nrc,
                'nit' => $request->nit,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
                'giro' => $request->giro,
                'notas' => $request->notas,
                'contacto_nombre' => $request->contacto_nombre,
                'contacto_cargo' => $request->contacto_cargo,
                'estado' => $request->estado,
            ]);

            return response()->json(['success' => 'El proveedor fue agregado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error: ' . $e->getMessage()], 500);
        }
    }

    public function updateProveedor(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'nrc' => 'required',
            'nit' => 'required',
            'telefono' => ['required', 'regex:/^[0-9]+$/'],
            'correo' => 'required|email',
            'direccion' => 'required',
            'notas' => 'required',
            'giro' => 'required',
            'contacto_nombre' => 'required',
            'contacto_cargo' => 'required',
            'estado' => 'required',
        ], [
            'nombre.required' => 'El nombre del proveedor es requerido',
            'nrc.required' => 'El NRC del proveedor es requerido',
            'nit.required' => 'El NIT del proveedor es requerido',
            'telefono.required' => 'El teléfono del proveedor es requerido',
            'telefono.regex' => 'El teléfono solo debe contener números',
            'correo.required' => 'El correo del proveedor es requerido',
            'correo.email' => 'Debe ingresar un correo válido',
            'direccion.required' => 'La dirección del proveedor es requerida',
            'notas.required' => 'La dirección del proveedor es requerida',
            'giro.required' => 'El giro del proveedor es requerido',
            'contacto_nombre.required' => 'El nombre del contacto es requerido',
            'contacto_cargo.required' => 'El cargo del contacto es requerido',
            'estado.required' => 'El estado del proveedor es requerido',
        ]);

        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['error' => 'No se encontro el detalle de este proveedor'], 422);
        }

        try {
            $proveedor->update([
                'nombre' => $request->nombre,
                'nrc' => $request->nrc,
                'nit' => $request->nit,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
                'giro' => $request->giro,
                'notas' => $request->notas,
                'contacto_nombre' => $request->contacto_nombre,
                'contacto_cargo' => $request->contacto_cargo,
                'estado' => $request->estado,
            ]);

            return response()->json(['success' => 'El proveedor fue actualizado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error: ' . $e->getMessage()], 500);
        }
    }


    public function deleteProveedor($id)
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return response()->json(['error' => 'No se encontro el proveedor'], 422);
        }

        try {
            $proveedor->delete();

            return response()->json(['success' => 'El proveedor se elimino con exito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error: ' . $e->getMessage()], 500);
        }
    }
}
