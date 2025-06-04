<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ventas\Sales;
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

        return view('layouts.app', compact('totalVentas', 'totalFacturas', 'ventasHoy', 'usuario', 'users', 'totalTransaccionesCuentasBancarias', 'totalPagosBancariosCheque'));
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
