<?php

namespace App\Http\Controllers\Cobros;

use App\Http\Controllers\Controller;
use App\Models\Bancos\Bancos;
use App\Models\Bancos\CuentasBancarias;
use App\Models\SociosNegocios\Clientes;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BancosController extends Controller
{
    public function index()
    {
        return view('bancos.index');
    }

    public function bancosgetIndexData(Request $request)
    {
        if ($request->ajax()) {
            $data = Bancos::getIndexBancos();
            return DataTables::of($data)
                ->addColumn('nombre', function ($data) {
                    return $data?->nombre ?? 'no hay banco';
                })
                ->addColumn('codigo', function ($data) {
                    return $data?->codigo ?? 'sin codigo';
                })
                ->addColumn('estado', function ($data) {
                    if ($data->estado == 1) {
                        return '<span class="badge badge-center rounded-pill bg-label-success"><i class="icon-base bx bx-check"></i></span> Activo';
                    } else {
                        return '<span class="badge badge-center rounded-pill bg-label-danger"><i class="icon-base bx bx-x-circle"></i></span> Bloqueado';
                    }
                })
                ->addColumn('acciones', function ($data) {
                    $editar = '';
                    $agregarCuentasBancarias = '';

                    $editar = '<a href="#" 
                                    class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-banco"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editBanco"
                                    data-id="' . $data->id . '"
                                    data-nombre="' . e($data->nombre) . '"
                                    data-codigo="' . e($data->codigo) . '"
                                    data-estado="' . e($data->estado) . '"
                                    title="Editar">
                                    <i class="bx bx-edit"></i>
                             </a>';
                    $agregarCuentasBancarias = '<a href="' . route('cuentas.indexCuentasBancarias', $data->id) . '" 
                                    class="btn btn-dark mt-mobile w-90 mx-2"
                                    title="Agregar cuentas bancarias">
                                    <i class="bx bx-plus"></i>
                             </a>';

                    return $editar . $agregarCuentasBancarias;
                })
                ->rawColumns(['acciones', 'estado'])->make(true);
        }
    }

    public function storeBanco(Request $request)
    {
        /**
         * 
         * Logica para guardar el banco
         */

        $request->validate([
            'nombre' => 'required',
            'codigo' => 'required',
            'estado' => 'required'
        ], [
            'nombre.required' => 'El nombre del banco es requerido',
            'codigo.required' => 'El codigo del banco es requerido',
            'estado.required' => 'El estado del banco es requerido',
        ]);


        $banco = Bancos::create([
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
            'estado' => $request->estado
        ]);

        $banco->save();

        if ($banco) {
            return response()->json(['success' => 'El banco fue agregado correctamente'], 200);
        }


        return response()->json(['error' => 'Algo salio mal al intentar agregar el banco'], 405);
    }

    public function indexCuentasBancarias($id)
    {
        $banco = Bancos::find($id);
        $clientes = Clientes::all();
        if (!$banco) {
            return response()->json(['error' => 'Error el banco al que quieres agregar cuentas bancarias no existe.'], 405);
        }
        return view('bancos.CuentasBancarias', compact('banco', 'clientes'));
    }

    public function indexGetCuentasBancarias($id)
    {
        $bancos = Bancos::find($id);
        $cuentasbancarias = $bancos->cuentas;
        return DataTables::of($cuentasbancarias)
            ->addColumn('numero_cuenta', function ($data) {
                return $data?->numero_cuenta ?? '';
            })
            ->addColumn('banco', function ($data) use ($bancos) {
                return $bancos?->nombre ?? 'sin banco';
            })
            ->addColumn('tipo_cuenta', function ($data) {
                if ($data->tipo_cuenta == 'corriente') {
                    return '<span class="badge badge-center rounded-pill bg-label-dark"><i class="icon-base bx bxs-bank"></i></span> CORRIENTE';
                } elseif ($data->tipo_cuenta == 'ahorro') {
                    return '<span class="badge badge-center rounded-pill bg-label-success"><i class="icon-base bx bxs-bank"></i></span> AHORRO';
                } elseif ($data->tipo_cuenta == 'credito') {
                    return '<span class="badge badge-center rounded-pill bg-label-danger"><i class="icon-base bx bxs-bank"></i></span> CREDITO';
                }
            })
            ->addColumn('titular', function ($data) {
                return $data?->clientes?->nombre ?? '';
            })
            ->addColumn('moneda', function ($data) {
                if ($data->moneda) {
                    return '<span class="badge badge-center rounded-pill bg-label-success"><i class="icon-base bx bx-dollar"></i></span> USD';
                } else {
                    return '<span class="badge badge-center rounded-pill bg-label-success"><i class="icon-base bx bx-x-circle"></i></span> OTRO';
                }
            })
            ->addColumn('estado', function ($data) {
                if ($data->estado == 1) {
                    return '<span class="badge badge-center rounded-pill bg-label-success"><i class="icon-base bx bx-check"></i></span> ACTIVO';
                } else {
                    return '<span class="badge badge-center rounded-pill bg-label-danger"><i class="icon-base bx bx-x-circle"></i></span> BLOQUEADO';
                }
            })
            ->addColumn('acciones', function ($data) {
                $editar = '';

                $editar = '<a href="#" 
                                    class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-cuentabancaria"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCuentaBancaria"
                                    data-id="' . $data->id . '"
                                    data-numero_cuenta="' . e($data->numero_cuenta) . '"
                                    data-tipo_cuenta="' . e($data->tipo_cuenta) . '"
                                    data-cliente_id="' . e($data->cliente_id) . '"
                                    data-estado="' . e($data->estado) . '"
                                    title="Editar cuenta bancaria">
                                    <i class="bx bx-edit"></i>
                             </a>';

                $agregarCuentasBancarias = '';

                return $editar . $agregarCuentasBancarias;
            })
            ->rawColumns(['acciones', 'tipo_cuenta', 'moneda', 'estado'])->make(true);
    }

    public function storeCuentasBancarias(Request $request, $bancoId)
    {
        /**
         * Logica para guardar cuentas bancarias
         */
        try {
            $cuentasbancarias = CuentasBancarias::create([
                'banco_id' => $bancoId,
                'numero_cuenta' => $request->numero_cuenta,
                'tipo_cuenta' => $request->tipo_cuenta,
                'cliente_id' => $request->cliente_id,
                'moneda' => 'USD',
                'estado' => $request->estado
            ]);

            $cuentasbancarias->save();
            if ($cuentasbancarias) {
                return response()->json(['success' => 'La cuenta bancaria se agrego correctamente'], 200);
            }

            return response()->json(['error' => 'Algo salio mal al querer insertar la cuenta bancaria'], 405);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function updateCuentaBancaria(Request $request, $cuentaId)
    {
        /**
         * 
         * Logica para actualizar las cuentas bancarias
         */
        try {
            $cuentasbancarias = CuentasBancarias::find($cuentaId);
            if (!$cuentasbancarias) {
                return response()->json(['error' => 'No se encontro la cuenta que quieres actualizar'], 405);
            }

            $cuentasbancarias->update([
                'numero_cuenta' => $request->numero_cuenta,
                'tipo_cuenta' => $request->tipo_cuenta,
                'cliente_id' => $request->cliente_id,
                'moneda' => 'USD',
                'estado' => $request->estado
            ]);

            if ($cuentasbancarias) {
                return response()->json(['success' => 'Se actualizo correctamente la cuenta bancaria'], 200);
            }

            return response()->json(['error' => 'Algo salio mal al querer actualizar la cuenta bancaria'], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }


    public function updateBanco(Request $request, $id)
    {

        /***
         * 
         * Logica para actualizar bancos
         */

        $request->validate([
            'nombre' => 'required',
            'codigo' => 'required',
            'estado' => 'required'
        ], [
            'nombre.required' => 'El nombre del banco es requerido',
            'codigo.required' => 'El codigo del banco es requerido',
            'estado.required' => 'El estado del banco es requerido',
        ]);

        $banco = Bancos::find($id);

        if (!$banco) {
            return response()->json(['error' => 'No se encontro el banco que quieres actualizar'], 405);
        }

        $banco->update([
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
            'estado' => $request->estado
        ]);

        if ($banco) {
            return response()->json(['success' => 'El banco se actualizo correctamente'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al querer actualizar el banco'], 405);
    }
}
