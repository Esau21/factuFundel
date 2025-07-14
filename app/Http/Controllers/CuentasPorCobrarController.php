<?php

namespace App\Http\Controllers;

use App\Models\CXC\Abono;
use App\Models\CXC\CuentasPorCobrar;
use App\Models\SociosNegocios\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CuentasPorCobrarController extends Controller
{
    public function index()
    {
        return view('cxc.index');
    }

    public function indexGetDataCxC(Request $request)
    {
        if ($request->ajax()) {
            $data = CuentasPorCobrar::getCxC();

            return DataTables::of($data)
                ->addColumn('sale', function ($data) {
                    return $data?->sale?->id ?? '';
                })
                ->addColumn('cliente', function ($data) {
                    return $data?->sale?->clientes?->nombre ?? '';
                })
                ->addColumn('monto', function ($data) {
                    return '$' . number_format($data->monto, 2) ?? 0;
                })
                ->addColumn('saldo_pendiente', function ($data) {
                    return '$' . number_format($data->saldo_pendiente, 2) ?? 0;
                })
                ->addColumn('metodo_pago', function ($data) {
                    if ($data->metodo_pago == '01') {
                        return '<span class="badge badge-center rounded-pill bg-label-primary me-1"><i class="icon-base bx bx-money"></i></span> Efectivo';
                    } elseif ($data->metodo_pago == '04') {
                        return '<span class="badge badge-center rounded-pill bg-label-success me-1"><i class="icon-base bx bx-receipt"></i></span> Cheque';
                    } elseif ($data->metodo_pago == '05') {
                        return '<span class="badge badge-center rounded-pill bg-label-danger me-1"><i class="icon-base bx bx-transfer"></i></span> Transferencia';
                    } else {
                        return '<span class="badge badge-center rounded-pill bg-label-secondary me-1"><i class="icon-base bx bx-help-circle"></i></span> Otro';
                    }
                })
                ->addColumn('acciones', function ($data) {
                    return 'Acciones';
                })
                ->rawColumns(['acciones', 'metodo_pago'])->make(true);
        }
    }



    public function abonosIndex()
    {
        $cuentasPorCobrarPendientes = CuentasPorCobrar::where('saldo_pendiente', '>', 0)->with('sale.clientes')->get();

        return view('cxc.abonos', compact('cuentasPorCobrarPendientes'));
    }

    public function registrarAbono(Request $request)
    {
        $request->validate([
            'cuenta_por_cobrar_id' => 'required|exists:cuentas_por_cobrar,id',
            'monto' => 'required|numeric|min:0.01',
            'fecha_abono' => 'required|date',
            'metodo_pago' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $cuenta = CuentasPorCobrar::findOrFail($request->cuenta_por_cobrar_id);

            if ($request->monto > $cuenta->saldo_pendiente) {
                return response()->json(['error' => 'El monto del abono no puede ser mayor al saldo pendiente'], 422);
            }

            /**
             * Crear el abono
             */
            Abono::create([
                'cuenta_por_cobrar_id' => $cuenta->id,
                'monto' => $request->monto,
                'fecha_abono' => $request->fecha_abono,
                'metodo_pago' => $request->metodo_pago,
            ]);

            /**
             * Actualizar saldo pendiente
             */
            $cuenta->saldo_pendiente -= $request->monto;

            /**
             * Si el saldo pendiente es 0, podrías actualizar estado o fecha de pago
             */
            if ($cuenta->saldo_pendiente <= 0) {
                $cuenta->saldo_pendiente = 0;
                $cuenta->fecha_pago = now();
            }

            $cuenta->save();

            DB::commit();

            return response()->json(['mensaje' => 'Abono registrado correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar abono: ' . $e->getMessage()], 405);
        }
    }
}
