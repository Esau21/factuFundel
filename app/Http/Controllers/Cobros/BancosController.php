<?php

namespace App\Http\Controllers\Cobros;

use App\Http\Controllers\Controller;
use App\Models\Bancos\Bancos;
use App\Models\Bancos\ChequeRecibido;
use App\Models\Bancos\CuentasBancarias;
use App\Models\SociosNegocios\Clientes;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
                                    <i class="bx bxs-bank"></i>
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
            'nombre' => 'required|unique:bancos,nombre',
            'codigo' => 'required',
            'estado' => 'required'
        ], [
            'nombre.required' => 'El nombre del banco es requerido',
            'nombre.unique' => 'Ya existe un banco con este nombre',
            'codigo.required' => 'El código del banco es requerido',
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
                return $data?->titular ?? '';
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
                                    data-titular="' . e($data->titular) . '"
                                    data-estado="' . e($data->estado) . '"
                                    title="Editar cuenta bancaria">
                                    <i class="bx bx-edit"></i>
                             </a>';

                return $editar;
            })
            ->rawColumns(['acciones', 'tipo_cuenta', 'moneda', 'estado'])->make(true);
    }

    public function storeCuentasBancarias(Request $request, $bancoId)
    {
        /**
         * Validaciones personalizadas
         */
        $request->validate([
            'numero_cuenta' => 'required|unique:cuentas_bancarias,numero_cuenta',

            'tipo_cuenta' => [
                'required',
                Rule::unique('cuentas_bancarias')->where(function ($query) use ($request, $bancoId) {
                    return $query->where('titular', $request->titular)
                        ->where('tipo_cuenta', $request->tipo_cuenta)
                        ->where('banco_id', $bancoId);
                }),
            ],

            'titular' => 'required',
            'estado' => 'required'
        ], [
            'numero_cuenta.required' => 'El número de cuenta es requerido',
            'numero_cuenta.unique' => 'Este número de cuenta ya está registrado',

            'tipo_cuenta.required' => 'El tipo de cuenta es requerido',
            'tipo_cuenta.unique' => 'Este titular ya tiene una cuenta de este tipo',

            'titular.required' => 'El titular es requerido',
            'estado.required' => 'El estado es requerido'
        ]);
        /**
         * Logica para guardar cuentas bancarias
         */
        try {
            $cuentasbancarias = CuentasBancarias::create([
                'banco_id' => $bancoId,
                'numero_cuenta' => $request->numero_cuenta,
                'tipo_cuenta' => $request->tipo_cuenta,
                'titular' => $request->titular,
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
        $cuentasbancarias = CuentasBancarias::find($cuentaId);
        if (!$cuentasbancarias) {
            return response()->json(['error' => 'No se encontró la cuenta que quieres actualizar'], 405);
        }

        $bancoId = $cuentasbancarias->banco_id;

        $request->validate([
            'numero_cuenta' => [
                'required',
                Rule::unique('cuentas_bancarias', 'numero_cuenta')->ignore($cuentaId),
            ],
            'tipo_cuenta' => [
                'required',
                Rule::unique('cuentas_bancarias')->where(function ($query) use ($request, $cuentaId, $bancoId) {
                    return $query->where('titular', $request->titular)
                        ->where('tipo_cuenta', $request->tipo_cuenta)
                        ->where('banco_id', $bancoId)
                        ->where('id', '!=', $cuentaId);
                }),
            ],
            'titular' => 'required',
            'estado' => 'required'
        ], [
            'numero_cuenta.required' => 'El número de cuenta es requerido',
            'numero_cuenta.unique' => 'Este número de cuenta ya está en uso por otra cuenta',

            'tipo_cuenta.required' => 'El tipo de cuenta es requerido',
            'tipo_cuenta.unique' => 'Este titular ya tiene una cuenta de este tipo en este banco',

            'titular.required' => 'El titular es requerido',
            'estado.required' => 'El estado es requerido'
        ]);

        $cuentasbancarias->update([
            'numero_cuenta' => $request->numero_cuenta,
            'tipo_cuenta' => $request->tipo_cuenta,
            'titular' => $request->titular,
            'moneda' => 'USD',
            'estado' => $request->estado
        ]);

        return response()->json(['success' => 'Se actualizó correctamente la cuenta bancaria'], 200);
    }


    public function updateBanco(Request $request, $id)
    {

        /***
         * 
         * Logica para actualizar bancos
         */

        $request->validate([
            'nombre' => 'required|unique:bancos,nombre,' . $id,
            'codigo' => 'required|unique:bancos,codigo,' . $id,
            'estado' => 'required'
        ], [
            'nombre.required' => 'El nombre del banco es requerido',
            'nombre.unique' => 'Ya existe un banco con este nombre',
            'codigo.required' => 'El código del banco es requerido',
            'codigo.unique' => 'Ya existe un banco con este código',
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

    public function indexCheques()
    {
        $clientes = Clientes::all();
        return view('bancos.cobros.chequeIndex', compact('clientes'));
    }

    public function getIndexDataCheque(Request $request)
    {
        if ($request->ajax()) {
            $data = ChequeRecibido::getIndexdata(
                $request->cliente_id,
                $request->fecha_inicio,
                $request->fecha_fin
            );
            return DataTables::of($data)
                ->addColumn('cliente', function ($data) {
                    return $data?->cliente?->nombre ?? '';
                })
                ->addColumn('cuenta', function ($data) {
                    $numeroCuenta = $data->cuenta->numero_cuenta ?? 'N/A';
                    $titular = $data->cuenta->titular ?? 'N/A';

                    return '<div class="bg-light border rounded p-2" style="min-width: 250px;">
                                <div><strong>Cuenta:</strong> ' . $numeroCuenta . '</div>
                                <div><strong>Titular:</strong> ' . $titular . '</div>
                            </div>';
                })
                ->addColumn('numero_cheque', function ($data) {
                    return 'Cheque #' . $data->numero_cheque ?? '';
                })
                ->addColumn('monto', function ($data) {
                    return '<span class="badge bg-label-success text-success fw-semibold px-3 py-2 rounded-pill"> $' . number_format($data->monto, 2) . '
                            </span>';
                })
                ->addColumn('fechaEmi', function ($data) {
                    return $data->fecha_emision
                        ? Carbon::parse($data->fecha_emision)->locale('es')->isoFormat('D [de] MMMM [de] YYYY')
                        : '';
                })
                ->addColumn('fechaPago', function ($data) {
                    return $data->fecha_pago
                        ? Carbon::parse($data->fecha_pago)->locale('es')->isoFormat('D [de] MMMM [de] YYYY')
                        : '';
                })
                ->addColumn('estado', function ($data) {
                    return $data->estado ?? '';
                })
                ->addColumn('observaciones', function ($data) {
                    return $data->observaciones ?? '';
                })
                ->addColumn('correlativo', function ($data) {
                    return $data->correlativo ?? '';
                })
                ->addColumn('acciones', function ($data) {
                    $imprimir = '<a href="' . route('cheques.generarCheque', $data->id) . '" 
                                    class="btn bg-label-info mt-mobile w-90 mx-2"
                                    title="Generar cheque" target="_blank">
                                    <i class="bx bx-file" style="font-size: 23px; transition: transform 0.2s;"></i>
                             </a>';

                    return $imprimir;
                })
                ->rawColumns(['monto', 'acciones', 'cuenta'])->make(true);
        }
    }

    public function descargarHistorialPDF(Request $request)
    {
        $clienteId = $request->input('cliente_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $cliente = null;
        if ($clienteId) {
            $cliente = Clientes::find($clienteId);
        }

        /* Obtener datos filtrados */
        $cheques = ChequeRecibido::getIndexdata($clienteId, $fechaInicio, $fechaFin);

        /* Aquí generas el PDF (usando por ejemplo DomPDF) */
        $pdf = Pdf::loadView('bancos.cobros.historialPDF', compact('cheques', 'cliente', 'fechaInicio', 'fechaFin'));

        /* Descargar PDF */
        return $pdf->stream('historial_cheques.pdf');
    }


    public function generarCheque($id)
    {
        $cheques = ChequeRecibido::with(['cliente', 'cuenta'])->find($id);
        if (!$cheques) {
            return response()->json(['error' => 'El cheque no fue encontrado', 405]);
        }

        $pdf = Pdf::loadView('bancos.cobros.cheque', compact('cheques'));

        return $pdf->stream('cheque-#' . $cheques->numero_cheque . '.pdf');
    }
}
