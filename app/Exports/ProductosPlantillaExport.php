<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductosPlantillaExport implements FromArray, WithHeadings, WithChunkReading, WithEvents
{
    public function array(): array
    {
        return [
            [
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'codigo',
            'nombre',
            'precio_compra',
            'precio_venta',
            'stock',
            'descripcion',
            'stock_minimo'
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function ($event) {
                $sheet = $event->sheet;

                foreach (range('A', 'M') as $column) {
                    $sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }

                $sheet->getDelegate()->getStyle($sheet->calculateWorksheetDimension())
                    ->getAlignment()->setWrapText(true);
            },
        ];
    }
}
