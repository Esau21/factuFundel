<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CajaController extends Controller
{
    public function index()
    {
        return view('cajas.index');
    }

    public function getIndexCaja(Request $request)
    {
        if ($request->ajax()) {
            $data = Caja::getindexData();

            return DataTables::of($data)
                ->addColumn('usuario', function ($data) {
                    return $data?->user?->name ?? '';
                })
                ->addColumn('fecha_apertura', function ($data) {
                    return $data?->fecha_apertura ?? '';
                })
                ->addColumn('fecha_cierre', function ($data) {
                    return $data?->fecha_cierre ?? '';
                })
                ->addColumn('monto_inicial', function ($data) {
                    return $data?->monto_inicial ?? '';
                })
                ->addColumn('total_efectivo', function ($data) {
                    return $data?->total_efectivo ?? '';
                })
                ->addColumn('total_tarjeta', function ($data) {
                    return $data?->total_tarjeta ?? '';
                })
                ->addColumn('total_otros', function ($data) {
                    return $data?->total_otros ?? '';
                })
                ->addColumn('total_declarado', function ($data) {
                    return $data?->total_declarado ?? '';
                })
                ->addColumn('diferencia', function ($data) {
                    return $data?->diferencia ?? '';
                })
                ->addColumn('estado', function ($data) {
                    return $data?->estado ?? '';
                })
                ->addColumn('observaciones', function ($data) {
                    return $data?->observaciones ?? '';
                })
                ->addColumn('acciones', function ($data) {
                    $cerrarCaja = '';
                    $eliminar = '';
                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeleteCaja({$data->id}); return false;";

                    if ($data->estado === 'abierta' && Auth()->user()->can('caja_cerrar')) {
                        $cerrarCaja = '<a href="#" 
                        class="btn bg-label-primary mt-mobile w-90 mx-2 btn-cerrar-caja"
                        data-bs-toggle="modal"
                        data-bs-target="#cerrarCajaModal"
                        data-id="' . $data->id . '"
                        title="Cerrar Caja">
                        <i class="bx bx-lock" style="font-size: 20px;"></i>
                     </a>';
                    }

                    if (Auth()->user()->can('caja_delete')) {
                        $eliminar = '<a title="Eliminar" class="btn bg-label-danger mt-mobile mx-2" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                        <i class="bx bx-trash-alt" style="font-size: 20px;"></i>
                     </a>';
                    }

                    return $cerrarCaja . $eliminar;
                })
                ->rawColumns(['acciones'])->make(true);
        }
    }

    public function storeCaja(Request $request)
    {
        $cajaAbierta = Caja::where('user_id', Auth::user()->id)
            ->where('estado', 'abierta')
            ->first();

        if ($cajaAbierta) {
            return response()->json(['error' => 'Ya tienes una caja abierta  - ' . Auth::user()->name], 405);
        }

        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $caja = Caja::create([
            'user_id' => Auth::user()->id,
            'fecha_apertura' => $request->fecha_apertura,
            'monto_inicial' => $request->monto_inicial,
            'estado' => 'abierta'
        ]);

        $caja->save();

        if ($caja) {
            return response()->json(['success' => 'La caja se aperturo correctamente'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al querer aperrturar la caja'], 422);
    }

    public function cerrarCaja(Request $request, $id)
    {
        $request->validate([
            'total_efectivo' => 'required|numeric|min:0',
            'total_tarjeta' => 'required|numeric|min:0',
            'total_otros' => 'required|numeric|min:0',
            'total_declarado' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $caja = Caja::findOrFail($id);

        if ($caja->estado !== 'abierta') {
            return response()->json(['error' => 'La caja ya esta cerrada'], 405);
        }

        $sumaSistema = $caja->monto_inicial + $request->total_efectivo + $request->total_tarjeta + $request->total_otros;
        $diferencia = $request->total_declarado - $sumaSistema;


        $caja->update([
            'fecha_cierre' => now(),
            'total_efectivo' => $request->total_efectivo,
            'total_tarjeta' => $request->total_tarjeta,
            'total_otros' => $request->total_otros,
            'total_declarado' => $request->total_declarado,
            'diferencia' => $diferencia,
            'estado' => 'cerrada',
            'observaciones' => $request->observaciones
        ]);

        if ($caja) {
            return response()->json(['success' => 'La caja se cerro exitosamente'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al querer cerrar la caja'], 405);
    }
}
