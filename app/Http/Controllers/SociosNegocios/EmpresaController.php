<?php

namespace App\Http\Controllers\SociosNegocios;

use App\Http\Controllers\Controller;
use App\Models\SociosNegocios\Empresa;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmpresaController extends Controller
{
    public function index()
    {
        return view('empresas.index');
    }

    public function indexGetDataEmpresa(Request $request)
    {
        if ($request->ajax()) {
            $empresas = Empresa::getDataEmpresa();
            return DataTables::of($empresas)
                ->addColumn('nombre', function ($data) {
                    return $data->nombre ?? 'sin datos de la empresa';
                })
                ->addColumn('nrc', function ($data) {
                    return $data->nrc ?? 'sin datos de la empresa';
                })
                ->addColumn('nit', function ($data) {
                    return $data->nit ?? 'sin datos de la empresa';
                })
                ->addColumn('giro', function ($data) {
                    return $data->giro ?? 'sin datos de la empresa';
                })
                ->addColumn('telefono', function ($data) {
                    return $data->telefono ?? 'sin datos de la empresa';
                })
                ->addColumn('correo', function ($data) {
                    return $data->correo ?? 'sin datos de la empresa';
                })
                ->addColumn('direccion', function ($data) {
                    return $data->direccion ?? 'sin datos de la empresa';
                })
                ->addColumn('logo', function ($data) {
                    return '<img src="' . $data->imagen . '" alt="no image" class="img-fluid rounded" width="50px;" height="50px;">';
                })
                ->addColumn('acciones', function ($data) {
                    $editar = '';
                    $editar = '<a href="' . route('empresas.edit', $data->id) . '" 
                                    class="btn btn-primary mt-mobile w-90 mx-2"
                                    title="Editar">
                                    <i class="bx bx-edit"></i>
                             </a>';

                    return $editar;
                })->rawColumns(['acciones', 'estado', 'logo'])->make(true);
        }
    }

    public function addviewEmpresa()
    {
        return view('empresas.add');
    }

    public function storeEmpresa(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:empresas,nombre',
            'nrc' => 'required',
            'nit' => 'required',
            'giro' => 'required',
            'telefono' => 'required',
            'correo' => 'required|email',
            'direccion' => 'required'
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.unique' => 'Este nombre de empresa ya está registrado.',
            'nrc.required' => 'El NRC es obligatorio.',
            'nit.required' => 'El NIT es obligatorio.',
            'giro.required' => 'El giro es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'direccion.required' => 'La dirección es obligatoria.'
        ]);

        /**
         * trabajaremos la funcion para crear la empresa con su logo
         */
        $empresas = new Empresa($request->except('logo'));

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $path = 'empresas';

            $storagePath = storage_path('app/public/' . $path);
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();

            $logo->move(public_path('storage/' . $path), $logoName);

            $empresas->logo = $path . '/' . $logoName;
        }

        if ($empresas) {
            $empresas->save();
            return response()->json(['success' => 'Empresa agregada con exito'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al agregar los datos de la empresa'], 422);
    }

    public function editarEmpresa($id)
    {
        $empresa = Empresa::find($id);
        return view('empresas.edit', compact('empresa'));
    }

    public function updateEmpresa(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|unique:empresas,nombre,' . $id,
            'nrc' => 'required',
            'nit' => 'required',
            'giro' => 'required',
            'telefono' => 'required',
            'correo' => 'required|email',
            'direccion' => 'required'
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.unique' => 'Este nombre de empresa ya está registrado.',
            'nrc.required' => 'El NRC es obligatorio.',
            'nit.required' => 'El NIT es obligatorio.',
            'giro.required' => 'El giro es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'direccion.required' => 'La dirección es obligatoria.'
        ]);

        $empresas = Empresa::find($id);
        if (!$empresas) {
            return response()->json(['error' => 'No se encontraron los datos de esta empresa']);
        }

        /* Actualizamos datos excepto el logo */
        $empresas->fill($request->except('logo'));

        /* Si hay un nuevo logo, procesamos */
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $path = 'empresas';
            $storagePath = public_path('storage/' . $path);

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            /* Eliminamos el logo anterior si existe */
            if ($empresas->logo && file_exists(public_path('storage/' . $empresas->logo))) {
                unlink(public_path('storage/' . $empresas->logo));
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->move($storagePath, $logoName);
            $empresas->logo = $path . '/' . $logoName;
        }

        $empresas->save();

        return response()->json(['success' => 'La empresa se modificó con éxito'], 200);
    }
}
