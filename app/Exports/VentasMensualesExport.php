<?php

namespace App\Exports;

use App\Models\SociosNegocios\Clientes;
use App\Models\Ventas\Sales;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VentasMensualesExport implements FromView
{
    protected $clienteId;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($clienteId, $fechaInicio, $fechaFin)
    {
        $this->clienteId = $clienteId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function view(): View
    {
        $ventas = Sales::with('clientes', 'users')
            ->when($this->clienteId, function ($q) {
                $q->where('cliente_id', $this->clienteId);
            })
            ->when($this->fechaInicio && $this->fechaFin, function ($q) {
                $q->whereBetween('fecha_venta', [$this->fechaInicio, $this->fechaFin]);
            })
            ->orderBy('fecha_venta', 'desc')
            ->get();

        $cliente = null;
        if ($this->clienteId) {
            $cliente = Clientes::find($this->clienteId);
        }

        $totalGeneral = $ventas->sum(function ($venta) {
            return ($venta->total + $venta->iva - $venta->retencion);
        });

        return view('saleMonth.excel.reporte-ventas-mes', [
            'ventas' => $ventas,
            'cliente' => $cliente,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin,
            'totalGeneral' => $totalGeneral
        ]);
    }
}
