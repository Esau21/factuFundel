<?php

namespace App\Http\Controllers\DGII;

use App\Exports\HistorialDTEExport;
use App\Http\Controllers\Controller;
use App\Models\CorrelativoDte;
use App\Models\DGII\DocumentosDte as DGIIDocumentosDte;
use App\Models\SociosNegocios\Clientes;
use App\Services\DteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DocumentosDTEController extends Controller
{
    public function index()
    {
        $clientes = Clientes::all();
        return view('dgii.documentoIndex', compact('clientes'));
    }

    public function indexGetDtaDocumentosDte(Request $request)
    {
        if ($request->ajax()) {
            $tipo = $request->get('tipo', '');
            $data = DGIIDocumentosDte::getData(
                $tipo,
                $request->cliente_id,
                $request->fecha_inicio,
                $request->fecha_fin
            );
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
                    if ($data->estado == 'FIRMADO') {
                        return '<span class="badge badge-center rounded-pill bg-label-success me-1"><i class="icon-base bx bx-receipt"></i></span>FIRMADO';
                    } elseif ($data->estado == 'ANULADO') {
                        return '<span class="badge badge-center rounded-pill bg-label-danger me-1"><i class="icon-base bx bx-receipt"></i></span>ANULADO';
                    } elseif ($data->estado == 'RECIBIDO') {
                        return '<span class="badge badge-center rounded-pill bg-label-primary me-1"><i class="icon-base bx bx-receipt"></i></span>RECIBIDO';
                    } elseif ($data->estado == 'RECHAZADO') {
                        return '<span class="badge badge-center rounded-pill bg-label-warning me-1"><i class="icon-base bx bx-receipt"></i></span>RECHAZADO';
                    } elseif ($data->estado == 'PROCESADO') {
                        return '<span class="badge badge-center rounded-pill bg-label-info me-1"><i class="icon-base bx bx-receipt"></i></span>PROCESADO';
                    }
                })
                ->addColumn('sello_recibido', function ($data) {
                    return $data->sello_recibido ?? '';
                })
                ->addColumn('acciones', function ($data) {
                    $resMH = '<a href="' . route('facturacion.viewMHResponse', $data->id) . '" 
                                        class="mx-1 d-inline-block btn btn-sm bg-label-primary" 
                                        title="Respuesta Hacienda"
                                        style="text-decoration: none;">
                                        <i class="bx bx-message-dots" 
                                            style="font-size: 28px; transition: transform 0.2s;">
                                        </i>
                            </a>';


                    $documento = '<a href="' . route('facturacion.generarDocumentoElectronico', $data->id) . '" 
                                    class="btn btn-sm mx-1 d-inline-block bg-label-success" 
                                    title="Mostrar Factura"
                                    style="text-decoration: none;">
                                    <i class="bx bxs-spreadsheet" 
                                        style="font-size: 28px; transition: transform 0.2s;">
                                    </i>
                                </a>';

                    $json = '<a href="' . route('facturacion.getDocumentoTributarioJson', $data->id) . '" 
                                    class="mx-1 d-inline-block btn btn-sm bg-label-secondary" 
                                    title="Descargar JSON"
                                    style="text-decoration: none;">
                                    <i class="bx bxs-file-json" 
                                        style="font-size: 28px; transition: transform 0.2s;">
                                    </i>
                            </a>';

                    $documentoDownload = '<a href="' . route('facturacion.descargarPDFTipoDocumento', $data->id) . '" 
                                    class="mx-1 d-inline-block btn btn-sm bg-label-danger" 
                                    title="Descargar Factura"
                                    style="text-decoration: none;" target="_blank">
                                    <i class="bx bxs-file-pdf" 
                                        style="font-size: 28px; transition: transform 0.2s;">
                                    </i>
                                </a>';

                    $anulacionJson = '';

                    if ($data->estado !== 'ANULADO') {
                        $anulacionJson = '<a href="#" 
                                    class="mx-1 d-inline-block btn btn-sm bg-label-warning btn-anular-json" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#anularJson"
                                    data-id="' . $data->id . '"
                                    data-tipo_documento="' . $data->tipo_documento . '"
                                    data-numero_control="' . $data->numero_control . '"
                                    data-codigo_generacion="' . $data->codigo_generacion . '"
                                    data-fecha_emision="' . $data->fecha_emision . '"
                                    title="Anular DTE"
                                    style="text-decoration: none;" target="_blank">
                                    <i class="bx bxs-shield-minus" 
                                        style="font-size: 28px; transition: transform 0.2s;">
                                    </i>
                                </a>';
                    }

                    return $resMH . $documento . $json . $documentoDownload . $anulacionJson;
                })
                ->rawColumns(['acciones', 'numero_control', 'codigo_generacion', 'tipo_documento', 'fecha_emision', 'estado'])->make(true);
        }
    }

    public function historialDTEFechasXlsx(Request $request)
    {
        $cliente_id = $request->get('cliente_id');
        $fecha_inicio = $request->get('fecha_inicio');
        $fecha_fin = $request->get('fecha_fin');

        return Excel::download(
            new HistorialDTEExport($cliente_id, $fecha_inicio, $fecha_fin),
            'historial_dte_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function anularDocumentoTributarioElectronico(Request $request, $id)
    {
        $documento = DGIIDocumentosDte::with('empresa')->findOrFail($id);

        try {
            // Pasar $request y $documento en el orden correcto
            $mhResponse = (new DteService())->anularDTE($request, $documento);

            $documento->update([
                'mh_response' => json_encode($mhResponse),
                'sello_recibido' => $mhResponse['selloRecibido'] ?? null,
                'estado' => 'anulado'
            ]);

            return response()->json(['success' => true, 'mh_response' => $mhResponse]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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

    public function viewMHResponse($documentoId)
    {

        $documentoDTE = DGIIDocumentosDte::find($documentoId);
        if (!$documentoDTE) {
            return response()->json(['error' => 'No existe el documento dte quer quieres visualizar']);
        }

        return view('dgii.mhResponse', compact('documentoDTE'));
    }

    public function correlativosDteIndex()
    {
        return view('dgii.dteCorrelativos');
    }

    public function correlativosDteIndexGetData(Request $request)
    {
        if ($request->ajax()) {
            $data = CorrelativoDte::getData();
            return DataTables::of($data)
                ->addColumn('tipo_dte', function ($data) {
                    if ($data->tipo_dte == '01') {
                        return '<span class="badge badge-center rounded-pill bg-label-primary me-1"><i class="icon-base bx bx-receipt"></i></span>' . $data->tipo_dte . ' | Factura';
                    } elseif ($data->tipo_dte == '03') {
                        return '<span class="badge badge-center rounded-pill bg-label-success me-1"><i class="icon-base bx bx-receipt"></i></span>' . $data->tipo_dte . ' | Comprobante de crédito fiscal';
                    } elseif ($data->tipo_dte == '14') {
                        return '<span class="badge badge-center rounded-pill bg-label-secondary me-1"><i class="icon-base bx bx-receipt"></i></span>' . $data->tipo_dte . ' | Sujeto Excluido';
                    } elseif ($data->tipo_dte == '15') {
                        return '<span class="badge badge-center rounded-pill bg-label-warning me-1"><i class="icon-base bx bx-receipt"></i></span>' . $data->tipo_dte . ' | Comprobante de Donacíon';
                    } else {
                        return '<span class="badge badge-center rounded-pill bg-label-danger me-1">Otro</span>';
                    }
                })
                ->addColumn('codigo_establecimiento', function ($data) {
                    return '<span class="badge bg-light border text-dark fw-semibold">' . e($data->codigo_establecimiento) . '</span>';
                })
                ->addColumn('correlativo', function ($data) {
                    return '<span class="badge bg-light border text-dark fw-semibold">' . e($data->correlativo) . '</span>';
                })->rawColumns(['tipo_dte', 'codigo_establecimiento', 'correlativo'])->make(true);
        }
    }


    public function generarDocumentoElectronico($documentoId)
    {
        $documento = DGIIDocumentosDte::with(['cliente', 'empresa'])->findOrFail($documentoId);

        switch ($documento->tipo_documento) {
            case '01': // Factura electrónica
                return $this->generarFacturaElectronica($documento);
                break;

            case '03': // CCF
                return $this->verCCF($documento);
                break;

            case '14': // Sujeto excluido
                return $this->generarFacturaSujetoExcluido($documento);
                break;

            case '15': // Donación
                return $this->generarComprobanteDonacion($documento);
                break;

            default:
                throw new \Exception("Tipo de documento no soportado: " . $documento->tipo_documento);
        }

        // Guardar el JSON generado (si aplica)
        $documento->json_dte = json_encode($jsonDte, JSON_UNESCAPED_UNICODE);
        $documento->save();

        return response()->json([
            'message' => 'DTE generado correctamente',
            'json_dte' => $jsonDte,
        ]);
    }

    protected function generarFacturaElectronica($documento)
    {
        // Logica para mostrar el documento de la factura
        return view('documentos.factura_consumidor_final', [
            'json' => json_decode($documento->json_dte, true),
            'mh' => json_decode($documento->mh_response, true),
        ]);
    }

    protected function verCCF($documento)
    {
        // Logica para mostrar el documento comprobante de credito fiscal
        return view('documentos.factura_comprobante_credito_fiscal', [
            'json' => json_decode($documento->json_dte, true),
            'mh' => json_decode($documento->mh_response, true),
        ]);
    }


    protected function generarFacturaSujetoExcluido($documento)
    {
        // Lógica para sujeto excluido
        return view('documentos.factura_sujeto_excluido', [
            'json' => json_decode($documento->json_dte, true),
            'mh' => json_decode($documento->mh_response, true),
        ]);
    }

    protected function generarComprobanteDonacion($documento)
    {
        // Lógica para donación
        return view('documentos.factura_comprobante_donacion', [
            'json' => json_decode($documento->json_dte, true),
            'mh' => json_decode($documento->mh_response, true),
        ]);
    }

    public function descargarPDFTipoDocumento($documentoId)
    {
        $documento = DGIIDocumentosDte::with(['cliente', 'empresa'])->findOrFail($documentoId);

        $view = match ($documento->tipo_documento) {
            '01' => 'documentos.pdf.fe',
            '03' => 'documentos.pdf.ccf',
            '14' => 'documentos.pdf.se',
            '15' => 'documentos.pdf.cd',
            default => abort(404, 'Tipo de documento no soportado.')
        };

        $json = json_decode($documento->json_dte, true);
        $mh = json_decode($documento->mh_response ?? '{}', true);

        $pdf = Pdf::loadView($view, compact('json', 'mh'))
            ->setPaper('A4')
            ->setOption('isHtml5ParserEnabled', true);


        return $pdf->stream("documento-{$documentoId}.pdf");
    }
}
