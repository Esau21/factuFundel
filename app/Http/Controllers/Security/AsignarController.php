<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class AsignarController extends Controller
{
    public function index()
    {
        $permisos = Permission::select('*')->orderBy('id', 'asc')->get();
        $roles = Role::select('*')->orderBy('id', 'asc')->get();
        return view('asignar.index', [
            'permisos' => $permisos,
            'roles' => $roles
        ]);
    }

    public function getDataIndexAsiganr(Request $request)
    {
        if ($request->ajax()) {
            $roleId = $request->role_id;

            if ($roleId == 'Elegir') {
                return response()->json(['error' => 'No se seleccionó un rol válido.'], 422);
            }

            $data = Permission::select('*')->orderBy('id', 'asc')->get();
            return DataTables::of($data)
                ->addColumn('name', function ($data) {
                    return $data?->name;
                })
                ->addColumn('acciones', function ($data) use ($roleId) {
                    $role = Role::find($roleId);
                    $isChecked = $role?->hasPermissionTo($data->name) ? 'checked' : '';
                    $asignar = '<div class="d-flex justify-content-center align-items-center">
                    <input class="form-check-input" type="checkbox" name="check" id="check-' . $data->id . '" data-id="' . $data->id . '" ' . $isChecked . '>
                </div>';

                    return $asignar;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }

    public function storeAsignarPermisosRoles(Request $request)
    {
        $request->merge([
            'assign' => filter_var($request->assign, FILTER_VALIDATE_BOOLEAN),
        ]);

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
            'assign' => 'required|boolean',
        ]);

        $role = Role::find($request->role_id);
        $permission = Permission::find($request->permission_id);

        if (!$role || !$permission) {
            return response()->json(['error' => 'Datos inválidos.'], 422);
        }

        if ($request->assign) {
            $role->givePermissionTo($permission);
            return response()->json([
                'success' => "Permiso '{$permission->name}' asignado correctamente al rol '{$role->name}'."
            ], 200);
        } else {
            $role->revokePermissionTo($permission);
            return response()->json([
                'success' => "Permiso '{$permission->name}' revocado del rol '{$role->name}'."
            ], 200);
        }
    }

    public function AsignarTodo(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        if (!$role) {
            return response()->json(['error' => 'Rol no encontrado'], 405);
        }

        $permisos = Permission::all();

        $role->givePermissionTo($permisos);

        return $role;

        return response()->json(['success' => "Todos los permisos fueron asignados al rol '{$role->name}'."], 200);
    }

    public function RevocarTodo(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        $role = Role::find($request->role_id);

        if (!$role) {
            return response()->json(['error' => 'Rol no encontrado'], 405);
        }

        $permissions = Permission::select('*')->get();

        $role->revokePermissionTo($permissions);

        return response()->json(['success' => "Todos los permisos fueron revocados del rol '{$role->name}'."], 200);
    }
}
