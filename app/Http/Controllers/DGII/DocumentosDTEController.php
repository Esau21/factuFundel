<?php

namespace App\Http\Controllers\DGII;

use App\Http\Controllers\Controller;
use App\Models\DGII\DocumentosDte as DGIIDocumentosDte;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class DocumentosDTEController extends Controller
{
    public function index()
    {
        return view('dgii.documentoIndex');
    }

    public function indexGetDtaDocumentosDte(Request $request)
    {
        if ($request->ajax()) {
            $tipo = $request->get('tipo', '');
            $data = DGIIDocumentosDte::getData($tipo);
            return DataTables::of($data)
                ->addColumn('tipo_documento', function ($data) {
                    if ($data->tipo_documento == '01') {
                        return '<span class="badge badge-center rounded-pill bg-label-primary me-1"><i class="icon-base bx bx-receipt"></i></span> Factura';
                    } elseif ($data->tipo_documento == '03') {
                        return '<span class="badge badge-center rounded-pill bg-label-success me-1"><i class="icon-base bx bx-receipt"></i></span> Comprobante de crédito fiscal';
                    } elseif ($data->tipo_documento == '14') {
                        return '<span class="badge badge-center rounded-pill bg-label-secondary me-1"><i class="icon-base bx bx-receipt"></i></span> Sujeto Excluido';
                    } elseif ($data->tipo_documento == '15') {
                        return '<span class="badge badge-center rounded-pill bg-label-warning me-1"><i class="icon-base bx bx-receipt"></i></span> Comprobante de Donacíon';
                    } else {
                        return '<span class="badge badge-center rounded-pill bg-label-danger me-1">Otro</span>';
                    }
                })
                ->addColumn('numero_control', function ($data) {
                    return '<span class="badge bg-light border text-dark fw-semibold">' . e($data->numero_control) . '</span>';
                })
                ->addColumn('codigo_generacion', function ($data) {
                    return '<span class="badge bg-light border text-dark fw-semibold">' . e($data->codigo_generacion) . '</span>';
                })
                ->addColumn('fecha_emision', function ($data) {
                    $fecha = Carbon::parse($data->fecha_emision)->locale('es')->translatedFormat('j \d\e F \d\e Y');
                    return e($fecha);
                })
                ->addColumn('cliente', function ($data) {
                    return $data?->cliente?->nombre ?? '';
                })
                ->addColumn('empresa', function ($data) {
                    return $data?->empresa?->nombre ?? '';
                })
                ->addColumn('estado', function ($data) {
                    return $data->estado ?? '';
                })
                ->addColumn('sello_recibido', function ($data) {
                    return $data->sello_recibido ?? '';
                })
                ->addColumn('acciones', function ($data) {
                    $sendMH = '<a href="' . route('clientes.edit', $data->id) . '" 
                    class="mx-1 d-inline-block" 
                    title="Respuesta Hacienda"
                    style="text-decoration: none;">
                    <i class="bx bxl-squarespace text-primary" 
                       style="font-size: 28px; transition: transform 0.2s;">
                    </i>
                </a>';

                    $json = '<a href="' . route('facturacion.getDocumentoTributarioJson', $data->id) . '" 
                    class="mx-1 d-inline-block" 
                    title="Descargar JSON"
                    style="text-decoration: none;">
                    <i class="bx bxs-file-json text-secondary" 
                       style="font-size: 28px; transition: transform 0.2s;">
                    </i>
                </a>';

                    return $sendMH . $json;
                })
                ->rawColumns(['acciones', 'numero_control', 'codigo_generacion', 'tipo_documento', 'fecha_emision'])->make(true);
        }
    }

    public function getDocumentoTributarioJson($documento_id)
    {
        $documentoTributario = DGIIDocumentosDte::find($documento_id);

        if (!$documentoTributario) {
            abort(404, 'Documento no encontrado.');
        }

        $uuid = $documentoTributario->codigo_generacion ?? 'documento';

        return response()->streamDownload(function () use ($documentoTributario) {
            echo $documentoTributario->json_dte;
        }, "$uuid.json", [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $uuid . '.json"',
        ]);
    }
}
