<?php

namespace App\Http\Controllers\SociosNegocios;

use App\Http\Controllers\Controller;
use App\Models\ActividadEconomica;
use App\Models\SociosNegocios\Empresa;
use App\Models\Ubicaciones\Departamento;
use App\Models\Ubicaciones\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Auth::user()->empresa;
        $now = now();
        $habilitarBoton = true;

        if ($empresa && $empresa->token_expire) {
            $horasRestantes = $now->diffInHours($empresa->token_expire, false);
            if ($horasRestantes > 4) {
                $habilitarBoton = false;
            }
        }

        return view('empresas.index', compact('habilitarBoton'));
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
                ->addColumn('actividad', function ($data) {
                    return $data?->actividad?->descActividad ?? 'sin datos de la empresa';
                })
                ->addColumn('departamento', function ($data) {
                    return $data?->departamento?->nombre ?? 'sin datos de la empresa';
                })
                ->addColumn('municipio', function ($data) {
                    return $data?->municipio?->nombre ?? 'sin datos de la empresa';
                })
                ->addColumn('telefono', function ($data) {
                    return $data->telefono ?? 'sin datos de la empresa';
                })
                ->addColumn('correo', function ($data) {
                    return $data->correo ?? 'sin datos de la empresa';
                })
                ->addColumn('complemento', function ($data) {
                    return $data->complemento ?? 'sin datos de la empresa';
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
        $municipio = Municipio::all();
        $departamento = Departamento::all();
        $actividad = ActividadEconomica::all();
        return view('empresas.add', compact('municipio', 'departamento', 'actividad'));
    }

    public function storeEmpresa(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:empresas,nombre',
            'nombreComercial' => 'required',
            'departamento_id' => 'required',
            'municipio_id' => 'required',
            'nrc' => 'required',
            'nit' => 'required',
            'actividad_economica_id' => 'required',
            'telefono' => 'required',
            'correo' => 'required|email',
            'complemento' => 'required',
            'ambiente' => 'required',
            'codPuntoVentaMH' => 'required',
            'codEstablecimientoMH' => 'required',
            'claveAPI' => 'required',
            'claveCert' => 'required'
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.unique' => 'Este nombre de empresa ya está registrado.',
            'nombreComercial.required' => 'El nombre comercial de la empresa es obligatorio.',
            'departamento_id.required' => 'El departamento es obligatorio',
            'municipio_id.required' => 'El municipio es obligatorio',
            'actividad_economica_id.required' => 'La actividad economica es obligatoria',
            'nrc.required' => 'El NRC es obligatorio.',
            'nit.required' => 'El NIT es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'complemento.required' => 'La dirección es obligatoria.'
        ]);

        /**
         * trabajaremos la funcion para crear la empresa con su logo
         */
        $empresas = new Empresa($request->except('logo', 'claveAPI', 'claveCert'));
        $empresas->token = 'Sin token';
        $empresas->token_expire = now()->subDay();
        $empresas->claveAPI = Crypt::encryptString($request->input('claveAPI'));
        $empresas->claveCert = Crypt::encryptString($request->input('claveCert'));

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
        $municipio = Municipio::all();
        $departamento = Departamento::all();
        $actividad = ActividadEconomica::all();
        return view('empresas.edit', compact('empresa', 'municipio', 'departamento', 'actividad'));
    }

    public function updateEmpresa(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|unique:empresas,nombre,' . $id,
            'nombreComercial' => 'required',
            'departamento_id' => 'required',
            'municipio_id' => 'required',
            'nrc' => 'required',
            'nit' => 'required',
            'actividad_economica_id' => 'required',
            'telefono' => 'required',
            'correo' => 'required|email',
            'complemento' => 'required',
            'ambiente' => 'required',
            'codPuntoVentaMH' => 'required',
            'codEstablecimientoMH' => 'required',
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.unique' => 'Este nombre de empresa ya está registrado.',
            'nombreComercial.required' => 'El nombre comercial de la empresa es obligatorio.',
            'departamento_id.required' => 'El departamento es obligatorio',
            'municipio_id.required' => 'El municipio es obligatorio',
            'actividad_economica_id.required' => 'La actividad economica es obligatoria',
            'nrc.required' => 'El NRC es obligatorio.',
            'nit.required' => 'El NIT es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'complemento.required' => 'La dirección es obligatoria.'
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

    public function generarNuevoToken()
    {
        $empresa = Auth::user()->empresa;

        if (!$empresa) {
            return back()->with('error', 'No se encontró la empresa.');
        }

        $response = Http::asMultipart()->post('https://apitest.dtes.mh.gob.sv/seguridad/auth', [
            [
                'name' => 'user',
                'contents' => $empresa->nit,
            ],
            [
                'name' => 'pwd',
                'contents' => Crypt::decryptString($empresa->claveAPI),
            ],
        ]);

        if ($response->successful() && isset($response['body']['token'])) {
            $token = $response['body']['token'];

            $empresa->token = 'Bearer ' . $token;
            $empresa->token_expire = now()->addDay();
            $empresa->save();

            return back()->with('success', 'Token actualizado correctamente.');
        }

        return back()->with('error', 'Error al actualizar el token.');
    }
}
