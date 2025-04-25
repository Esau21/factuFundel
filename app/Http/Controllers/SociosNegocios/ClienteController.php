<?php

namespace App\Http\Controllers\SociosNegocios;

use App\Http\Controllers\Controller;
use App\Models\SociosNegocios\Clientes;
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
                ->addColumn('tipo_documento', fn($data) => $data->tipo_documento)
                ->addColumn('numero_documento', fn($data) => $data->numero_documento)
                ->addColumn('nit', fn($data) => $data->nit ?? 'sin data')
                ->addColumn('nrc', fn($data) => $data->nrc ?? 'sin data')
                ->addColumn('giro', fn($data) => $data->giro ?? 'sin data')
                ->addColumn('direccion', fn($data) => $data->direccion)
                ->addColumn('departamento', fn($data) => $data->departamento)
                ->addColumn('municipio', fn($data) => $data->municipio)
                ->addColumn('telefono', fn($data) => $data->telefono ?? 'sin data')
                ->addColumn('correo_electronico', fn($data) => $data->correo_electronico ?? 'sin data')
                ->addColumn('tipo_contribuyente', fn($data) => $data->tipo_contribuyente)
                ->addColumn('codigo_actividad', fn($data) => $data->codigo_actividad ?? 'sin data')
                ->addColumn('tipo_persona', fn($data) => $data->tipo_persona)
                ->addColumn('es_extranjero', fn($data) => $data->es_extranjero ? 'Sí' : 'No')
                ->addColumn('pais', fn($data) => $data->pais ?? 'sin data')
                ->addColumn('acciones', function ($data) {
                    $editar = '<a href="#" 
                                class="btn btn-primary btn-sm btn-editar-cliente"
                                data-bs-toggle="modal"
                                data-bs-target="#editCliente"
                                data-id="' . $data->id . '"
                                data-nombre="' . e($data->nombre) . '"
                                title="Editar">
                                <i class="bx bx-edit"></i>
                            </a>';

                    $eliminar = '<a href="javascript:void(0)" 
                                  class="btn btn-danger btn-sm" 
                                  onclick="confirmDeleteCliente(' . $data->id . ')"
                                  title="Eliminar">
                                  <i class="bx bx-trash-alt"></i>
                                </a>';

                    return $editar . ' ' . $eliminar;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }



    public function addCliente()
    {
        return view('clientes.add');
    }

    public function storeCliente(Request $request)
    {
        $request->validate([
            'tipo_persona' => 'required|in:natural,juridica',
            'nombre' => 'required|string|max:255',
            'tipo_documento' => 'required|in:DUI,NIT,PASAPORTE,CEDULA',
            'numero_documento' => 'required_if:tipo_persona,natural|string|max:20', /* Solo obligatorio si es natural */
            'direccion' => 'required|string|max:255',
            'departamento' => 'required|string',
            'municipio' => 'required|string',
            'telefono' => 'nullable|string|max:20',
            'correo_electronico' => 'nullable|email|max:255',
            'tipo_contribuyente' => 'required|string',

            /* Solo si es persona jurídica */
            'nrc' => 'nullable|required_if:tipo_persona,juridica|string|max:20',  /* Permite null si no es requerido */
            'giro' => 'nullable|required_if:tipo_persona,juridica|string|max:255', /* Permite null si no es requerido */

            /* Solo si es extranjero */
            'pais' => 'required_if:es_extranjero,1|string|max:100',
            'es_extranjero' => 'nullable|boolean',
        ], [
            'tipo_persona.required' => 'Debes especificar si es persona natural o jurídica.',
            'nrc.required_if' => 'El NRC es obligatorio para personas jurídicas.',
            'giro.required_if' => 'El giro comercial es obligatorio para personas jurídicas.',
            'pais.required_if' => 'El país es obligatorio si el cliente es extranjero.',
            'numero_documento.required_if' => 'El número de documento es obligatorio para personas naturales.',
            'nrc.string' => 'El NRC debe ser una cadena de texto.',
            'giro.string' => 'El giro debe ser una cadena de texto.',
        ]);


        $clientes = Clientes::create([
            'nombre' => $request->nombre,
            'tipo_documento' => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nit' => $request->nit ?: null,
            'nrc' => $request->nrc ?: null,
            'giro' => $request->giro ?: null,
            'direccion' => $request->direccion,
            'departamento' => $request->departamento,
            'municipio' => $request->municipio,
            'telefono' => $request->telefono ?: null,
            'correo_electronico' => $request->correo_electronico ?: null,
            'tipo_contribuyente' => $request->tipo_contribuyente,
            'codigo_actividad' => $request->codigo_actividad ?: null,
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
}
