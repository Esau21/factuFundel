<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\SociosNegocios\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        $empresas = Empresa::all();

        return view('users.index', compact('users', 'roles', 'empresas'));
    }

    public function getDataUsersIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = User::getDataUsers();
            return DataTables::of($data)
                ->addColumn('name', function ($data) {
                    return $data->name ?? '';
                })
                ->addColumn('empresa', function($data){
                    return $data?->empresa ?? 'sin datos de empresa';
                })
                ->addColumn('email', function ($data) {
                    return $data->email ?? '';
                })
                ->addColumn('profile', function ($data) {
                    return $data->profile;
                })
                ->addColumn('status', function ($data) {
                    if ($data->status == 'Active') {
                        return '<span class="badge bg-success">Activo</span>';
                    } elseif ($data->status == 'Locked') {
                        return '<span class="badge bg-danger">Bloqueado</span>';
                    }
                    return $data->status;
                })
                ->addColumn('acciones', function ($data) {
                    $editar = '';

                    $ver = '<a href="' . route('usuarios.showUser', $data->id) . '" title="Ver" class="btn btn-warning mx-1">
                               <i class="bx bxl-trip-advisor"></i>
                           </a>';

                    $editar = '<a href="#" 
                                class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-user"
                                data-bs-toggle="modal"
                                data-bs-target="#editUser"
                                data-id="' . $data->id . '"
                                data-name="' . e($data->name) . '"
                                data-email="' . e($data->email) . '"
                                data-profile="' . e($data->profile) . '"
                                data-status="' . $data->status . '"
                                data-empresa_id="' . $data->empresa_id . '"
                                title="Editar">
                                <i class="bx bx-edit"></i>
                                </a>';

                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeleteUser({$data->id}); return false;";
                    $eliminar = '<a title="Eliminar" class="btn btn-danger  mx-1" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                                     <i class="bx bx-trash-alt"></i>
                                 </a>';

                    return '<div class="d-flex justify-content-center">' . $editar . $ver . $eliminar . '</div>';
                })
                ->rawColumns(['acciones', 'status'])
                ->make(true);
        }
    }

    public function showUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'No se encontrol el rol.']);
        }

        return view('users.show', compact('user'));
    }

    public function MyProfile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        if($user->status === 'Locked'){
            return redirect()->route('usuarios.index')->with('error', 'No puedes actualizar tu perfil porque tu cuenta está bloqueada.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('usuarios.index')->with('success', 'Perfil actualizado correctamente.');
    }

    public function agregarUsuario()
    {

        $roles = Role::select('*')->orderBy('id', 'asc')->get();
        return view('users.add', [
            'roles' => $roles,
        ]);
    }


    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required',
            'profile' => 'required|exists:roles,name',
            'status' => 'required|string',
            'empresa_id' => 'required',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo ya existe, por favor usa otro.',
            'password.required' => 'La contraseña es obligatoria.',
            'profile.required' => 'El perfil es obligatorio.',
            'status.required' => 'El estado es obligatorio.',
            'empresa_id' => 'La empresa para el usuario es requerida.'
        ]);


        if ($request->input('profile') === 'ROOT') {
            return response()->json(['error' => 'Solo debe existir un usuario ROOT'], 405);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'profile' => $request->input('profile'),
            'status' => $request->input('status'),
            'empresa_id' => $request->input('empresa_id'),
        ]);

        $user->syncRoles($request->input('profile'));

        return response()->json(['success' => 'Usuario agregado con éxito'], 200);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'required',
            'profile' => 'required|exists:roles,name',
            'status' => 'required|string',
            'empresa_id' => 'required',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo ya existe, por favor usa otro.',
            'password.required' => 'La contraseña es obligatoria.',
            'profile.required' => 'El perfil es obligatorio.',
            'status.required' => 'El estado es obligatorio.',
            'empresa_id' => 'La empresa para el usuario es requerida.'
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'upss algo salio mal al actualizar el usuario intante mas tarde']);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'profile' => $request->profile,
            'status' => $request->status,
            'empresa_id' => $request->input('empresa_id'),
        ]);

        $user->syncRoles($request->input('profile'));

        return response()->json(['success' => 'El usuario se actualizo correctamente']);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'No se puede eliminar el usuario del sistema.'], 405);
        } elseif ($user->profile == 'ROOT') {
            return response()->json(['error' => 'No se puede eliminar el usuario root'], 405);
        } elseif (Auth::id() == $user->id) {
            return response()->json(['error' => 'No puedes eliminar el usuario autenticado'], 405);
        } elseif ($user->empresa){
            return response()->json(['error' => 'No se puede eliminar el usuario ya que tiene asiganada una empresa.'], 405);
        }

        $user->delete();

        return response()->json(['success' => 'Se elimino el usuario del sistema']);
    }
}
