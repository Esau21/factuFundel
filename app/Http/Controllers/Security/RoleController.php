<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        return view('roles.index', compact('roles'));
    }

    public function getDataRoles(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select('*')->orderBy('id', 'asc')->get();
            return DataTables::of($data)
                ->addColumn('name', function ($data) {
                    return $data?->name;
                })
                ->addColumn('acciones', function ($data) {
                    $editar =  '';

                    $editar = '<a href="#" 
                                class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-role"
                                data-bs-toggle="modal"
                                data-bs-target="#editRole"
                                data-id="' . $data->id . '"
                                data-name="' . e($data->name) . '"
                                title="Editar">
                                <i class="bx bx-edit"></i>
                                </a>';

                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeleteRole({$data->id}); return false;";
                    $eliminar = '<a title="Eliminar" class="btn btn-danger mx-1" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                                    <i class="bx bx-trash-alt"></i>
                                 </a>';

                    return $editar .  $eliminar;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }


    public function StoreRole(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'El nombre del rol es requerido.',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        $role->save();

        return response()->json(['success' => 'Se guardo correctamente el rol']);
    }

    public function updateRoles(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'No se pudo actualizar el rol'], 422);
        }

        if ($role->name === 'ROOT') {
            return response()->json(['error' => 'No se pudo editar este rol'], 400);
        }

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => 'Se actualizo correctamente el role']);
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['error' => 'No se puede encontrar el rol a eliminar'], 422);
        } elseif ($role->name == 'ROOT') {
            return response()->json(['error' => 'No puedes eliminar el rol ROOT'], 405);
        } elseif ($role->users()->exists()) {
            return response()->json(['error' => 'No puedes eliminar el rol ya que esta asigando a multiples usuarios.'], 405);
        }

        $role->delete();

        return response()->json(['success' => 'Se elimino correctamente el rol']);
    }
}
