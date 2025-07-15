<?php

namespace App\Http\Controllers\DGII;

use App\Exports\DteLector;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LectorJsonController extends Controller
{
    public function index()
    {
        return view('lectorJson.index');
    }

    public function uploadFile(Request $request)
    {
        $archivos = $request->file('file');

        $archivos = $request->file('file');
        if (!is_array($archivos)) {
            $archivos = [$archivos];
        }


        if (!$archivos || count($archivos) === 0) {
            return response()->json(['error' => 'Debes seleccionar al menos un archivo JSON.'], 400);
        }

        if (count($archivos) > 500) {
            return response()->json(['error' => 'Solo se permiten hasta 500 archivos por proceso.'], 400);
        }

        $resultados = [];

        foreach ($archivos as $archivo) {
            $contenido = file_get_contents($archivo->getPathname());

            if (substr($contenido, 0, 3) === "\xEF\xBB\xBF") {
                $contenido = substr($contenido, 3);
            }

            if (!mb_check_encoding($contenido, 'UTF-8')) {
                return response()->json(['error' => 'Uno de los archivos tiene una codificación inválida.'], 400);
            }

            $json = json_decode($contenido, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Archivo JSON inválido: ' . json_last_error_msg()], 400);
            }

            $tipoDte = $json['identificacion']['tipoDte'] ?? '';
            $identificacion = $json['identificacion'] ?? [];
            $emisor = $json['emisor'] ?? [];
            $receptor = $json['receptor'] ?? [];
            $sujetoExcluido = $json['sujetoExcluido'] ?? [];
            $donante = $json['donante'] ?? [];
            $donatario = $json['donatario'] ?? [];
            $resumen = $json['resumen'] ?? [];
            $cuerpo = $json['cuerpoDocumento'] ?? [];

            $general = [
                'Tipo DTE' => $tipoDte,
                'Número Control' => $identificacion['numeroControl'] ?? '',
                'Código Generación' => $identificacion['codigoGeneracion'] ?? '',
                'Fecha Emisión' => $identificacion['fecEmi'] ?? '',
                'Hora Emisión' => $identificacion['horEmi'] ?? '',
                'Estado' => 'Procesado',
                'Nombre Entidad' => ($tipoDte === '14' ? $sujetoExcluido : ($tipoDte === '15' ? $donante : $receptor))['nombre'] ?? '',
                'NIT Entidad' => ($tipoDte === '14' ? $sujetoExcluido : ($tipoDte === '15' ? $donante : $receptor))['numDocumento'] ?? '',
                'NRC Entidad' => ($tipoDte === '14' ? $sujetoExcluido : ($tipoDte === '15' ? $donante : $receptor))['nrc'] ?? '',
                'Correo Entidad' => ($tipoDte === '14' ? $sujetoExcluido : ($tipoDte === '15' ? $donante : $receptor))['correo'] ?? '',
                'Dirección Entidad' => ($tipoDte === '14' ? $sujetoExcluido : ($tipoDte === '15' ? $donante : $receptor))['direccion']['complemento'] ?? '',
                'Empresa Emisora' => $emisor['nombre'] ?? ($donatario['nombre'] ?? ''),
                'NIT Emisor' => $emisor['nit'] ?? ($donatario['numDocumento'] ?? ''),
                'Correo Emisor' => $emisor['correo'] ?? ($donatario['correo'] ?? ''),
            ];

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

            foreach ($cuerpo as $item) {
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

                $resultados[] = array_merge($general, $itemData, $resumenDatos);
            }
        }

        /**
         * Descargar como excel
         */
        return Excel::download(new DteLector($resultados), 'dte_convertidos.xlsx');
    }
}
