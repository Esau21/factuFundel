<?php

namespace App\Http\Controllers\SociosNegocios;

use App\Http\Controllers\Controller;
use App\Models\ActividadEconomica;
use App\Models\SociosNegocios\Clientes;
use App\Models\SociosNegocios\Empresa;
use App\Models\Ubicaciones\Departamento;
use App\Models\Ubicaciones\Municipio;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    public function index()
    {
        return view('clientes.index');
    }

    public function getIndexDataClientes(Request $request)
    {
        if ($request->ajax()) {
            /* Obtenemos el tipo de cliente del parámetro 'tipo' (si existe) */
            $tipo = $request->get('tipo', ''); /* Valor predeterminado es vacío si no se pasa */

            /* Obtenemos los datos filtrados a través de getDtaClientes */
            $data = Clientes::getDtaClientes($tipo);

            return DataTables::of($data)
                ->addColumn('nombre', fn($data) => $data->nombre)
                ->addColumn('nombreComercial', fn($data) => $data->nombreComercial)
                ->addColumn('tipo_documento', fn($data) => $data->tipo_documento)
                ->addColumn('numero_documento', fn($data) => $data->numero_documento)
                ->addColumn('nit', fn($data) => $data->nit ?? 'sin data')
                ->addColumn('nrc', fn($data) => $data->nrc ?? 'sin data')
                ->addColumn('actividad', fn($data) => $data?->actividad?->descActividad ?? 'sin data')
                ->addColumn('direccion', fn($data) => $data->direccion)
                ->addColumn(
                    'departamento',
                    fn($data) => $data?->departamento
                        ? 'Código - ' . $data->departamento->codigo . ' | Departamento: ' . $data->departamento->nombre
                        : ''
                )
                ->addColumn(
                    'municipio',
                    fn($data) => $data?->municipio
                        ? 'Código - ' . $data->municipio->codigo . ' | Municipio: ' . $data->municipio->nombre
                        : ''
                )
                ->addColumn('telefono', fn($data) => $data->telefono ?? 'sin data')
                ->addColumn('correo_electronico', fn($data) => $data->correo_electronico ?? 'sin data')
                ->addColumn('tipo_contribuyente', fn($data) => $data->tipo_contribuyente)
                ->addColumn('tipo_persona', fn($data) => $data->tipo_persona)
                ->addColumn('es_extranjero', fn($data) => $data->es_extranjero ? 'Sí' : 'No')
                ->addColumn('pais', fn($data) => $data->pais ?? 'sin data')
                ->addColumn('acciones', function ($data) {
                    $editar = '<a href="' . route('clientes.edit', $data->id) . '" 
                                    class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-categoria"
                                    title="Editar cliente">
                                    <i class="bx bx-edit"></i>
                                </a>';

                    return $editar;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }



    public function addCliente()
    {
        $empresas = Empresa::all();
        $departamentos = Departamento::all();
        $municipios = Municipio::all();
        $actividad = ActividadEconomica::all();
        return view('clientes.add', compact('empresas', 'departamentos', 'municipios', 'actividad'));
    }

    public function storeCliente(Request $request)
    {
        $request->validate([
            'tipo_persona' => 'required|in:natural,juridica',
            'nombre' => 'required|string|max:255',
            'nombreComercial' => 'required',
            'tipo_documento' => 'required|in:DUI,NIT,PASAPORTE,CEDULA',
            'numero_documento' => 'required_if:tipo_persona,natural|string|max:20', /* Solo obligatorio si es natural */
            'direccion' => 'required|string|max:255',
            'departamento_id' => 'required|string',
            'municipio_id' => 'required|string',
            'telefono' => 'nullable|string|max:20',
            'correo_electronico' => 'nullable|email|max:255',
            'tipo_contribuyente' => 'required|string',

            /* Solo si es persona jurídica */
            'nrc' => 'nullable|required_if:tipo_persona,juridica|string|max:20',  /* Permite null si no es requerido */
            'actividad_economica_id' => 'nullable|required_if:tipo_persona,juridica|string|max:255', /* Permite null si no es requerido */

            /* Solo si es extranjero */
            'pais' => 'required_if:es_extranjero,1|string|max:100',
            'es_extranjero' => 'nullable|boolean',
        ], [
            'tipo_persona.required' => 'Debes especificar si es persona natural o jurídica.',
            'nrc.required_if' => 'El NRC es obligatorio para personas jurídicas.',
            'actividad_economica_id.required_if' => 'La actividad economica es obligatoria para personas jurídicas.',
            'pais.required_if' => 'El país es obligatorio si el cliente es extranjero.',
            'numero_documento.required_if' => 'El número de documento es obligatorio para personas naturales.',
            'nrc.string' => 'El NRC debe ser una cadena de texto.',
        ]);


        $clientes = Clientes::create([
            'nombre' => $request->nombre,
            'nombreComercial' => $request->nombreComercial,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nit' => $request->nit ?: null,
            'nrc' => $request->nrc ?: null,
            'actividad_economica_id' => $request->actividad_economica_id ?: null,
            'direccion' => $request->direccion,
            'departamento_id' => $request->departamento_id,
            'municipio_id' => $request->municipio_id,
            'telefono' => $request->telefono ?: null,
            'correo_electronico' => $request->correo_electronico ?: null,
            'tipo_contribuyente' => $request->tipo_contribuyente,
            'tipo_persona' => $request->tipo_persona,
            'es_extranjero' => $request->boolean('es_extranjero'),
            'pais' => $request->pais ?: null
        ]);

        $clientes->save();

        if ($clientes) {
            return response()->json(['success' => 'Cliente agregado correctamente al sistema'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al intentar agregar el cliente'], 422);
    }

    public function editarCliente($id)
    {
        $cliente = Clientes::find($id);
        if (!$cliente) {
            return response()->json(['error' => 'No se pudo encontrar el detalle de este cliente.', 422]);
        }

        $departamentos = Departamento::all();
        $municipios = Municipio::all();
        $actividad = ActividadEconomica::all();

        return view('clientes.edit', [
            'cliente' => $cliente,
            'departamentos' => $departamentos,
            'municipios' => $municipios,
            'actividad' => $actividad
        ]);
    }

    public function updateCliente(Request $request, $id)
    {
        $request->validate([
            'tipo_persona' => 'required|in:natural,juridica',
            'nombre' => 'required|string|max:255',
            'nombreComercial' => 'required',
            'tipo_documento' => 'required|in:DUI,NIT,PASAPORTE,CEDULA',
            'numero_documento' => 'required_if:tipo_persona,natural|string|max:20', /* Solo obligatorio si es natural */
            'direccion' => 'required|string|max:255',
            'departamento_id' => 'required|string',
            'municipio_id' => 'required|string',
            'telefono' => 'nullable|string|max:20',
            'correo_electronico' => 'nullable|email|max:255',
            'tipo_contribuyente' => 'required|string',

            /* Solo si es persona jurídica */
            'nrc' => 'nullable|required_if:tipo_persona,juridica|string|max:20',  /* Permite null si no es requerido */
            'actividad_economica_id' => 'nullable|required_if:tipo_persona,juridica|string|max:255', /* Permite null si no es requerido */

            /* Solo si es extranjero */
            'pais' => 'required_if:es_extranjero,1|string|max:100',
            'es_extranjero' => 'nullable|boolean',
        ], [
            'tipo_persona.required' => 'Debes especificar si es persona natural o jurídica.',
            'nrc.required_if' => 'El NRC es obligatorio para personas jurídicas.',
            'actividad_economica_id.required_if' => 'La actividad economica es obligatoria para personas jurídicas.',
            'pais.required_if' => 'El país es obligatorio si el cliente es extranjero.',
            'numero_documento.required_if' => 'El número de documento es obligatorio para personas naturales.',
            'nrc.string' => 'El NRC debe ser una cadena de texto.',
        ]);

        $clientes = Clientes::find($id);
        if (!$clientes) {
            return response()->json(['error' => 'No se puede actualizar el registro de este cliente'], 422);
        }

        $clientes->update([
            'nombre' => $request->nombre,
            'nombreComercial' => $request->nombreComercial,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nit' => $request->nit ?: null,
            'nrc' => $request->nrc ?: null,
            'actividad_economica_id' => $request->actividad_economica_id ?: null,
            'direccion' => $request->direccion,
            'departamento_id' => $request->departamento_id,
            'municipio_id' => $request->municipio_id,
            'telefono' => $request->telefono ?: null,
            'correo_electronico' => $request->correo_electronico ?: null,
            'tipo_contribuyente' => $request->tipo_contribuyente,
            'tipo_persona' => $request->tipo_persona,
            'es_extranjero' => $request->boolean('es_extranjero'),
            'pais' => $request->pais ?: null
        ]);

        if ($clientes) {
            return response()->json(['success' => 'Genial has actualiazdo el detalle del cliente'], 200);
        }
        return response()->json(['error' => 'Algo salio mal al intentar actualizar el cliente'], 422);
    }
}
