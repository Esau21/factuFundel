<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{

    public function index()
    {
        return view('permissions.index');
    }

    public function getIndexDataPermisos(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::select('*')->orderBy('id', 'asc')->get();
            return DataTables::of($data)
                ->addColumn('name', function ($data) {
                    return $data->name;
                })
                ->addColumn('acciones', function ($data) {
                    $editar =  '';
                    $editar = '<a href="#" 
                                class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-role"
                                data-bs-toggle="modal"
                                data-bs-target="#editPermiso"
                                data-id="' . $data->id . '"
                                data-name="' . e($data->name) . '"
                                title="Editar">
                                <i class="bx bx-edit"></i>
                                </a>';

                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeletePermiso({$data->id}); return false;";
                    $eliminar = '<a title="Eliminar" class="btn btn-danger mx-1" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                                    <i class="bx bx-trash-alt"></i>
                                 </a>';

                    return $editar .  $eliminar;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'El nombre del permiso es requerido',
        ]);

        $permisos = Permission::create([
            'name' => $request->name,
        ]);

        if (!$permisos) {
            return response()->json(['error' => 'Algo salio mal al agregar el permiso por favor revisa de nuevo.'], 422);
        }

        $permisos->save();

        return response()->json(['success' => 'Se agrego correctamente el permiso.'], 200);
    }

    public function updatePermiso(Request $request, $id)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'El nombre del permiso es requerido',
        ]);

        $permissions = Permission::find($id);

        if (!$permissions) {
            return response()->json(['error' => 'ups algo salio mal al intentar actualizar el permiso.'], 405);
        }

        $permissions->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => 'Se actualizo correctamente el permiso.'], 200);
    }

    public function deletePermiso($id)
    {
        $permissions = Permission::find($id);

        if (!$permissions) {
            return response()->json(['error' => 'No se puede eliminar el permiso, por que algo salio mal.'], 405);
        }

        $permissions->delete();

        return response()->json(['success' => 'El permiso fue eliminado coreccatmente.'], 200);
    }
}
