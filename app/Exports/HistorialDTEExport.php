<?php

namespace App\Exports;

use App\Models\DGII\DocumentosDte;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class HistorialDTEExport implements FromCollection, WithHeadings
{
    protected $cliente_id;
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct($cliente_id = null, $fecha_inicio = null, $fecha_fin = null)
    {
        $this->cliente_id = $cliente_id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {
        return DocumentosDte::with('cliente', 'empresa')
            ->when($this->cliente_id, fn($q) => $q->where('cliente_id', $this->cliente_id))
            ->when(
                $this->fecha_inicio && $this->fecha_fin,
                fn($q) =>
                $q->whereBetween('fecha_emision', [
                    Carbon::parse($this->fecha_inicio)->startOfDay(),
                    Carbon::parse($this->fecha_fin)->endOfDay()
                ])
            )
            ->get()
            ->flatMap(function ($dte) {
                $json = json_decode($dte->json_dte, true);
                $tipoDte = $json['identificacion']['tipoDte'] ?? '';

                $identificacion = $json['identificacion'] ?? [];
                $emisor = $json['emisor'] ?? [];
                $receptor = $json['receptor'] ?? [];
                $sujetoExcluido = $json['sujetoExcluido'] ?? [];
                $donante = $json['donante'] ?? [];
                $donatario = $json['donatario'] ?? [];
                $resumen = $json['resumen'] ?? [];
                $cuerpo = $json['cuerpoDocumento'] ?? [];

                /**
                 * Datos generales comunes
                 */
                $general = [
                    'Tipo DTE' => $tipoDte,
                    'Número Control' => $identificacion['numeroControl'] ?? '',
                    'Código Generación' => $identificacion['codigoGeneracion'] ?? '',
                    'Fecha Emisión' => $identificacion['fecEmi'] ?? '',
                    'Hora Emisión' => $identificacion['horEmi'] ?? '',
                    'Estado' => $dte->estado,
                ];

                /**
                 * Entidad destino
                 */
                if ($tipoDte === '14') {
                    $entidad = $sujetoExcluido;
                } elseif ($tipoDte === '15') {
                    $entidad = $donante;
                } else {
                    $entidad = $receptor;
                }

                $general += [
                    'Nombre Entidad' => $entidad['nombre'] ?? '',
                    'NIT Entidad' => $entidad['numDocumento'] ?? ($entidad['nit'] ?? ''),
                    'NRC Entidad' => $entidad['nrc'] ?? '',
                    'Correo Entidad' => $entidad['correo'] ?? '',
                    'Dirección Entidad' => $entidad['direccion']['complemento'] ?? '',
                ];

                /**
                 * Emisor / Donatario
                 */
                $general += [
                    'Empresa Emisora' => $emisor['nombre'] ?? ($donatario['nombre'] ?? ''),
                    'NIT Emisor' => $emisor['nit'] ?? ($donatario['numDocumento'] ?? ''),
                    'Correo Emisor' => $emisor['correo'] ?? ($donatario['correo'] ?? ''),
                ];

                /**
                 * Datos del resumen
                 */
                if ($tipoDte === '14') {
                    $resumenDatos = [
                        'Total Compra' => $resumen['totalCompra'] ?? '',
                        'Renta Retenida' => $resumen['reteRenta'] ?? '',
                        'Total Pagar' => $resumen['totalPagar'] ?? '',
                        'Total Letras' => $resumen['totalLetras'] ?? '',
                        'Observaciones' => $resumen['observaciones'] ?? '',
                    ];
                } elseif ($tipoDte === '15') {
                    $resumenDatos = [
                        'Valor Total' => $resumen['valorTotal'] ?? '',
                        'Total Letras' => $resumen['totalLetras'] ?? '',
                    ];
                } else {
                    $resumenDatos = [
                        'Total Gravada' => $resumen['totalGravada'] ?? '',
                        'Total Exenta' => $resumen['totalExenta'] ?? '',
                        'Total No Sujeta' => $resumen['totalNoSuj'] ?? '',
                        'IVA' => $resumen['totalIva'] ?? ($resumen['tributos'][0]['valor'] ?? ''),
                        'Total Pagar' => $resumen['totalPagar'] ?? '',
                        'Total Letras' => $resumen['totalLetras'] ?? '',
                    ];
                }

                /**
                 * Mapear los ítems
                 */
                return collect($cuerpo)->map(function ($item) use ($general, $resumenDatos, $tipoDte) {
                    if ($tipoDte === '14') {
                        $itemData = [
                            'Descripción Producto' => $item['descripcion'] ?? '',
                            'Cantidad' => $item['cantidad'] ?? '',
                            'Precio Unitario' => $item['precioUni'] ?? '',
                            'Total Compra' => $item['compra'] ?? '',
                        ];
                    } elseif ($tipoDte === '15') {
                        $itemData = [
                            'Descripción Donación' => $item['descripcion'] ?? '',
                            'Cantidad' => $item['cantidad'] ?? '',
                            'Valor Unitario' => $item['valorUni'] ?? '',
                            'Valor Donado' => $item['valor'] ?? '',
                            'Depreciación' => $item['depreciacion'] ?? '',
                        ];
                    } else {
                        $itemData = [
                            'Descripción Producto' => $item['descripcion'] ?? '',
                            'Cantidad' => $item['cantidad'] ?? '',
                            'Precio Unitario' => $item['precioUni'] ?? '',
                            'Venta Gravada' => $item['ventaGravada'] ?? '',
                            'Tributo (Código)' => is_array($item['tributos'] ?? null) ? implode(', ', $item['tributos']) : '',
                        ];
                    }

                    return array_merge($general, $itemData, $resumenDatos);
                });
            });
    }

    public function headings(): array
    {
        return [
            'Tipo DTE',
            'Número Control',
            'Código Generación',
            'Fecha Emisión',
            'Hora Emisión',
            'Estado',
            'Nombre Entidad',
            'NIT Entidad',
            'NRC Entidad',
            'Correo Entidad',
            'Dirección Entidad',
            'Empresa Emisora',
            'NIT Emisor',
            'Correo Emisor',
            'Descripción Producto',
            'Cantidad',
            'Precio Unitario',
            'Venta Gravada',
            'Tributo (Código)',
            'Total Gravada',
            'Total Exenta',
            'Total No Sujeta',
            'IVA',
            'Total Pagar',
            'Total Letras',
            'Total Compra',
            'Renta Retenida',
            'Observaciones',
            'Descripción Donación',
            'Valor Unitario',
            'Valor Donado',
            'Depreciación',
            'Valor Total',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                /**
                 * Columnas desde A hasta AG
                 */
                $columns = [
                    'A',
                    'B',
                    'C',
                    'D',
                    'E',
                    'F',
                    'G',
                    'H',
                    'I',
                    'J',
                    'K',
                    'L',
                    'M',
                    'N',
                    'O',
                    'P',
                    'Q',
                    'R',
                    'S',
                    'T',
                    'U',
                    'V',
                    'W',
                    'X',
                    'Y',
                    'Z',
                    'AA',
                    'AB',
                    'AC',
                    'AD',
                    'AE',
                    'AF',
                    'AG',
                ];

                foreach ($columns as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
