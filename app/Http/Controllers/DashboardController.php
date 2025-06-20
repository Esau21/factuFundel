<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ventas\Sales;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVentas = Sales::sum('total');
        $totalFacturas = Sales::count();
        $ventasHoy = Sales::whereDate('created_at', today())->sum('total');
        $usuario = Auth::user();
        $users = User::count();
        $totalTransaccionesCuentasBancarias = Sales::whereNotNull('cuenta_bancaria_id')->count();
        $totalPagosBancariosCheque = Sales::whereNotNull('cheque_bancario_id')->sum('total');

        $metaMensual = 50000;
        $ventasMesActual = Sales::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $porcentajeCrecimiento = $metaMensual > 0
            ? round(($ventasMesActual / $metaMensual) * 100, 2)
            : 0;

        $yearCurrent = now()->year;
        $ventasActual = Sales::whereYear('created_at', $yearCurrent)->sum('total');
        $ventasSiguiente = Sales::whereYear('created_at', $yearCurrent + 1)->sum('total');



        $activeThreshold = Carbon::now()->subMinutes(30)->timestamp;

        $usuariosActivos = DB::table('sessions')
            ->where('last_activity', '>=', $activeThreshold)
            ->distinct('user_id')
            ->pluck('user_id')
            ->filter()
            ->toArray();

        $totalUsuariosActivos = User::whereIn('id', $usuariosActivos)
            ->where('status', 'Active')
            ->count();

        $totalUsuarios = User::where('status', 'Active')->count();
        $usuariosActivosPorDia = collect();

        for ($i = 5; $i >= 0; $i--) {
            $start = Carbon::today()->subDays($i)->timestamp;
            $end = Carbon::today()->subDays($i - 1)->timestamp;

            $ids = DB::table('sessions')
                ->whereBetween('last_activity', [$start, $end])
                ->distinct('user_id')
                ->pluck('user_id')
                ->filter()
                ->toArray();

            $count = User::whereIn('id', $ids)
                ->where('status', 'Active')
                ->count();

            $usuariosActivosPorDia->push($count);
        }


        return view('layouts.app', compact(
            'totalVentas',
            'totalFacturas',
            'ventasHoy',
            'usuario',
            'users',
            'totalTransaccionesCuentasBancarias',
            'totalPagosBancariosCheque',
            'porcentajeCrecimiento',
            'ventasActual',
            'ventasSiguiente',
            'totalUsuariosActivos',
            'totalUsuarios',
            'usuariosActivosPorDia'
        ));
    }

    public function ventasPorMes()
    {
        try {
            $ventas = DB::table('sales')
                ->selectRaw('MONTH(fecha_venta) as mes, SUM(total) as total')
                ->whereYear('fecha_venta', now()->year)
                ->groupByRaw('MONTH(fecha_venta)')
                ->orderBy('mes')
                ->get();


            $datos = [];
            for ($i = 1; $i <= 12; $i++) {
                $total = $ventas->firstWhere('mes', $i)->total ?? 0;
                $datos[] = round($total, 2);
            }

            return response()->json($datos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
