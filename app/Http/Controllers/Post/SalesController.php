<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Bancos\Bancos;
use App\Models\Bancos\ChequeRecibido;
use App\Models\Bancos\CuentasBancarias;
use App\Models\DGII\DocumentosDte;
use App\Models\Producto\Producto;
use App\Models\SociosNegocios\Clientes;
use App\Models\SociosNegocios\Empresa;
use App\Models\Ventas\Sales;
use App\Models\Ventas\SalesDetails;
use App\Services\DteGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        $clientes = Clientes::all();
        $bancos = Bancos::all();
        $cuentas_bancarias = CuentasBancarias::all();
        return view('postSales.index', compact('productos', 'clientes', 'bancos', 'cuentas_bancarias'));
    }

    public function buscarProductos(Request $request)
    {
        $query = $request->input('query');

        /* Filtra los productos que coinciden con el término de búsqueda */
        $productos = Producto::where('nombre', 'LIKE', "%$query%")
            ->orWhere('codigo', 'LIKE', "%$query%")
            ->get();

        /* Aseguramos de que cada producto tenga la URL completa de la imagen */
        $productos->map(function ($producto) {
            $producto->imagen_url = $producto->imagen;
            return $producto;
        });

        return response()->json($productos);
    }

    //armamos el DTE
    public static function identificacionGet($version, $tipoDte, $ambiente, $codigoGeneracion, $fecha, $hora, $numeroControl, $tipoContingencia = null)
    {
        if ($tipoContingencia != null) {
            $tipoModelo = 2;
            $tipoOperacion = 2;
            $tipoContingencia = (int)$tipoContingencia;
        } else {
            $tipoModelo = 1;
            $tipoOperacion = 1;
            $tipoContingencia = null;
        }

        $identificacion = [
            "version" => (int)$version,
            "ambiente" => (string)$ambiente,
            "tipoDte" => (string)$tipoDte,
            "numeroControl" => (string)$numeroControl,
            "codigoGeneracion" => (string)$codigoGeneracion,
            "tipoModelo" => $tipoModelo,
            "tipoOperacion" => $tipoOperacion,
            "tipoContingencia" => $tipoContingencia,
            "motivoConti" => null,
            "fechaEmi" => (string)$fecha,
            "horaEmi" => (string)$hora,
            "moneda" => "USD"
        ];

        return $identificacion;
    }

    public static function obtenerEmisor(
        array $empresa,
        string $tipo_dte,
        string $nrc,
        string $nit,
        string $codActividad,
        string $descActividad,
        string $nombreComercial,
        string $tipoEstablecimiento,
        string $complemento,
        string $telefono,
        string $correo
    ): array {
        // No sobrescribas $empresa aquí, úsalo directamente
        $departamento = $empresa['departamento']['codigo'] ?? 'No definido';
        $municipio = $empresa['municipio']['codigo'] ?? 'No definido';

        $emisor = [
            "nrc" => (string)$nrc,
            "nit" => (string)$nit,
            "nombre" => (string)$empresa['nombre'],
            "nombreComercial" => (string)$nombreComercial,
            "codActividad" => (string)$codActividad,
            "descActividad" => (string)$descActividad,
            "direccion" => [
                "departamento" => $departamento,
                "municipio" => $municipio,
                "complemento" => $complemento
            ],
            "telefono" => $telefono,
            "correo" => $correo
        ];

        if ($tipo_dte == "01" || $tipo_dte == "03") {
            $emisor['nombreComercial'] = (string)$nombreComercial;
            $emisor['tipoEstablecimiento'] = (string)$tipoEstablecimiento;
        } elseif ($tipo_dte == "05" || $tipo_dte == "06") {
            $emisor['nombreComercial'] = (string)$nombreComercial;
            $emisor['tipoEstablecimiento'] = (string)$tipoEstablecimiento;
        } elseif ($tipo_dte == "14") {
            // No agregas nombreComercial ni tipoEstablecimiento para tipo 14
        } else {
            return []; // Retorna vacío si el tipo no coincide
        }

        return ['emisor' => $emisor];
    }


    public static function getReceptor($tipo_dte, $receptor)
    {
        $nombre = trim($receptor['nombre']);
        $tipo_documento = isset($receptor['tipo_documento']) ? (string)$receptor['tipo_documento'] : null;
        $nrc = isset($receptor['nrc']) ? (string)str_replace('-', '', $receptor['nrc']) : null;
        $numDocumento = isset($receptor['numDocumento']) ? (string)$receptor['numDocumento'] : null;
        $codActividad = isset($receptor['codActividad']) ? (string)$receptor['codActividad'] : null;
        $descActividad = isset($receptor['descActividad']) ? (string)$receptor['descActividad'] : null;

        $departamento = isset($receptor['departamento']) ? (string)$receptor['departamento'] : null;
        $municipio = isset($receptor['municipio']) ? (string)$receptor['municipio'] : null;
        $complemento = isset($receptor['direccion']) ? (string)$receptor['direccion'] : null;
        $telefono = isset($receptor['telefono']) ? (string)str_replace('-', '', $receptor['telefono']) : null;
        $correo = isset($receptor['correo']) ? (string)$receptor['correo'] : null;

        $dataReceptorDte = [];

        switch ($tipo_dte) {
            case "01":
                $dataReceptorDte = [
                    "tipoDocumento" => (string)$tipo_documento,
                    "numDocumento" => $numDocumento,
                    "nrc" => null,
                    "nombre" => $nombre,
                    "codActividad" => null,
                    "descActividad" => null,
                    "direccion" => [
                        "departamento" => $departamento,
                        "municipio" => $municipio,
                        "complemento" => $complemento
                    ],
                    "telefono" => (string)$telefono,
                    "correo" => (string)$correo
                ];
                break;

            case "03":
                $dataReceptorDte = [
                    "nit" => $numDocumento,
                    "nrc" => $nrc,
                    "nombre" => $nombre,
                    "codActividad" => $codActividad,
                    "descActividad" => $descActividad,
                    "nombreComercial" => $nombre,
                    "direccion" => [
                        "departamento" => $departamento,
                        "municipio" => $municipio,
                        "complemento" => $complemento
                    ],
                    "telefono" => (string)$telefono,
                    "correo" => (string)$correo
                ];
                break;
            default:
                $dataReceptorDte = [];
                break;
        }
        return $dataReceptorDte;
    }

    private function generarNumeroControl(string $tipoDte, string $codigoEstablecimiento, int $correlativo): string
    {
        return sprintf(
            'DTE-%02s-%08s-%015d',
            $tipoDte,
            $codigoEstablecimiento,
            $correlativo
        );
    }



    public function getResumen(Sales $sale): array
    {
        $sumas = $sale->details->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });

        $descu = $sale->descuento ?? 0;
        $descu = round($descu, 2);

        $iva = $sumas * 0.13;
        $iva = round($iva, 2);

        $total = $sumas + $iva - $descu;
        $total = round($total, 2);

        return [
            "totalNoSuj" => 0.00,                 // Total no sujeto (DTE tipo 03: 0.00)
            "totalExenta" => 0.00,                // Total exento (DTE tipo 03: 0.00)
            "totalGravada" => round($sumas, 2),   // Total gravado (sin IVA)
            "descuNoSuj" => 0.00,                 // Descuento no sujeto (normalmente 0.00)
            "descuExenta" => 0.00,                // Descuento exento (normalmente 0.00)
            "descuGravada" => $descu,             // Descuento sobre operaciones gravadas
            "porcentajeDescuento" => 0.00,        // Porcentaje descuento global si aplica
            "subTotal" => round($sumas, 2),       // Sumas antes de IVA
            "ivaRete1" => 0.00,                   // Retención 1% si es sujeto excluido (tipo 14)
            "ivaPercibido" => 0.00,               // Percepción de IVA si aplica
            "iva" => $iva,                        // IVA 13%
            "fovial" => 0.00,                     // FOVIAL si aplica (normalmente solo combustible)
            "cotrans" => 0.00,                    // COTRANS si aplica
            "subTotalVentas" => round($sumas, 2), // Igual que sumas antes de IVA
            "totalPagar" => $total,               // Total a pagar final
            "saldoFavor" => 0.00,                 // No aplica en DTE tipo 03
            "condicionOperacion" => "01",         // 01 = al contado, 02 = crédito
            "pagos" => [
                [
                    "codigo" => "01",             // Código de forma de pago (01 = efectivo)
                    "montoPago" => $total         // Monto total pagado
                ]
            ]
        ];
    }

    public function generarBodyDocumento(Request $request): array
    {
        $productos = [];
        $sumas = 0.00;
        $descuentoTotal = 0.00;

        foreach ($request->producto_id as $index => $productoId) {
            $cantidad = (int) $request->cantidad[$index];
            $precioUnitario = (float) $request->precio_unitario[$index];
            $descuento = isset($request->descuento_en_dolar[$index]) ? (float) $request->descuento_en_dolar[$index] : 0.00;

            $ventaNoGravada = 0.00; // Para DTE tipo 03 será 0
            $ventaGravada = round(($cantidad * $precioUnitario) - $descuento, 2);
            $iva = round($ventaGravada * 0.13, 2);
            $ventaConIVA = round($ventaGravada + $iva, 2);

            $sumas += $ventaGravada;
            $descuentoTotal += $descuento;

            $productos[] = [
                "numItem" => $index + 1,
                "tipoItem" => (integer)Producto::find($productoId)->items->codigo, // Bien
                "cantidad" => $cantidad,
                "codigo" => (string)$productoId,
                "codTributo" => null,
                "uniMedida" => (integer)Producto::find($productoId)->unidad->codigo, // Unidad
                "descripcion" => Producto::find($productoId)->nombre,
                "precioUni" => round($precioUnitario, 2),
                "montoDescu" => round($descuento, 2),
                "ventaNoSuj" => 0.00,
                "ventaExenta" => 0.00,
                "ventaGravada" => $ventaGravada,
                "tributos" => [ // IVA
                    [
                        "codigo" => "IVA",
                        "descripcion" => "Impuesto al Valor Agregado 13%",
                        "valor" => $iva
                    ]
                ],
                "psv" => 0.00, // Precio de venta sugerido
                "noGravado" => 0.00
            ];
        }

        $iva = round($sumas * 0.13, 2);
        $total = round($sumas + $iva - $descuentoTotal, 2);

        $resumen = [
            "totalNoSuj" => 0.00,
            "totalExenta" => 0.00,
            "totalGravada" => round($sumas, 2),
            "descuNoSuj" => 0.00,
            "descuExenta" => 0.00,
            "descuGravada" => round($descuentoTotal, 2),
            "porcentajeDescuento" => 0.00,
            "subTotal" => round($sumas, 2),
            "ivaRete1" => 0.00,
            "ivaPercibido" => 0.00,
            "iva" => round($iva, 2),
            "fovial" => 0.00,
            "cotrans" => 0.00,
            "subTotalVentas" => round($sumas, 2),
            "totalPagar" => $total,
            "saldoFavor" => 0.00,
            "condicionOperacion" => $request->tipo_venta == 1 ? '01' : '02',
            "pagos" => [
                [
                    "codigo" => $request->tipo_pago == 'cheque' ? '02' : '01',
                    "montoPago" => $total
                ]
            ]
        ];

        return [
            "productos" => $productos,
            "resumen" => $resumen
        ];
    }





    public function generarSale(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_venta' => 'required|in:1,2',
            'tipo_documento' => 'required|in:factura,ccf,nota_credito,nota_debito,factura_sujeto_excluido,comprobante_donacion',
            'tipo_pago' => 'required_if:tipo_venta,1|nullable|string',
            'total' => 'required|numeric|min:0',
            'producto_id' => 'required|array',
            'producto_id.*' => 'exists:productos,id',
            'cantidad.*' => 'required|integer|min:1',
            'precio_unitario.*' => 'required|numeric|min:0',
            'sub_total.*' => 'required|numeric|min:0',
            'descuento_porcentaje.*' => 'nullable|numeric|min:0|max:100',
            'descuento_en_dolar.*' => 'nullable|numeric|min:0',
            'plazos' => 'required_if:tipo_venta,2|nullable|integer|min:1',
            'tipo_plazo' => 'required_if:tipo_venta,2|string',
            'abono' => 'required_if:tipo_venta,2|nullable|numeric|min:0',
            'saldo_pendiente' => 'required_if:tipo_venta,2|numeric|min:0',
            'cuenta_bancaria_id' => 'required_if:tipo_pago,cheque,transferencia|exists:cuentas_bancarias,id',
            'numero_cheque' => 'required_if:tipo_pago,cheque|string',
            'fecha_emision' => 'required_if:tipo_pago,cheque|date',
            'cambio' => 'required_if:tipo_venta,1|numeric|min:0',
            'estado' => 'nullable|string',
            'observaciones' => 'nullable|string',
            // Validar venta relacionada si es nota de crédito o débito
            'venta_relacionada_id' => 'required_if:tipo_documento,nota_credito,nota_debito|nullable|exists:sales,id',
        ]);

        DB::beginTransaction();
        try {
            $tipo_pago = $request->tipo_pago;

            $tipo_dte = match ($request->tipo_documento) {
                'factura' => '01',
                'ccf' => '03',
                'nota_credito' => '05',
                'nota_debito' => '06',
                'factura_sujeto_excluido' => '14',
                'comprobante_donacion' => '15',
                default => null
            };

            $bodyDocumento = $this->generarBodyDocumento($request);
            $codigoGeneracion = strtoupper(Str::uuid());
            $fecha = now()->format('Y-m-d');
            $hora = now()->format('H:i:s');
            $ultimoDTE = DocumentosDte::where('tipo_documento', $tipo_dte)->max('id');
            $correlativo = $ultimoDTE ? $ultimoDTE + 1 : 1;
            $codigoEstablecimiento = $empresa->codigo_establecimiento ?? '1'; // o '00000001' si ya viene con ceros
            $numeroControl = $this->generarNumeroControl($tipo_dte, $codigoEstablecimiento, $correlativo);
            $identificacion = self::identificacionGet(
                1,
                $tipo_dte,
                '00',
                $codigoGeneracion,
                $fecha,
                $hora,
                $numeroControl,
                null // tipoContingencia
            );
            $cliente = Clientes::findOrFail($request->cliente_id);
            $receptor = self::getReceptor($tipo_dte, [
                'nombre' => $cliente->nombre,
                'tipo_documento' => $cliente->tipo_documento,
                'numDocumento' => $cliente->numero_documento,
                'nrc' => $cliente->nrc,
                'codActividad' => $cliente->actividad->codActividad,
                'descActividad' => $cliente->actividad->descActividad,
                'departamento' => $cliente->departamento->codigo,
                'municipio' => $cliente->municipio->codigo,
                'direccion' => $cliente->direccion,
                'telefono' => $cliente->telefono,
                'correo' => $cliente->correo_electronico,
            ]);
            $empresa = Auth::user()->empresa->load('actividad', 'departamento', 'municipio');
            $emisorData = self::obtenerEmisor(
                $empresa->toArray(),
                $tipo_dte,
                $empresa->nrc,
                $empresa->nit,
                $empresa->actividad->codActividad,
                $empresa->actividad->descActividad,
                $empresa->nombreComercial,
                $empresa->tipo_establecimiento ?? '01',
                $empresa->complemento ?? '',
                $empresa->telefono ?? '',
                $empresa->correo ?? ''
            );


            $cuerpoDocumento = $bodyDocumento['productos'];

            if ($tipo_dte === '01' || $tipo_dte === '03') {
                $jsonDTE['dteJson'] = [];
                $jsonDTE['dteJson']['identificacion'] = $identificacion;
                $jsonDTE['dteJson']['documentoRelacionado'] = null;
                $jsonDTE['dteJson']['emisor'] = $emisorData['emisor'];
                $jsonDTE['dteJson']['receptor'] = $receptor;
                $jsonDTE['dteJson']['otrosDocumentos'] = null;
                $jsonDTE['dteJson']['ventaTercero'] = null;
                $jsonDTE['dteJson']['cuerpoDocumento'] = $cuerpoDocumento;
                $jsonDTE['dteJson']['resumen'] = $bodyDocumento['resumen'];
                $jsonDTE['dteJson']['extension'] = [
                    "nombEntrega" => null,
                    "docuEntrega" => null,
                    "nombRecibe" => null,
                    "docuRecibe" => null,
                    "observaciones" => null,
                    "placaVehiculo" => null,
                ];
                $jsonDTE['dteJson']['apendice'] = null;
            } elseif ($tipo_dte === '14') {
                $jsonDTE['dteJson'] = [
                    "identificacion" => $identificacion,
                    "emisor" => $emisorData['emisor'],
                    "sujetoExcluido" => $receptor,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => $bodyDocumento['resumen'],
                    "apendice" => null,
                ];
            }

            $documentoDte = DocumentosDte::create([
                'tipo_documento' => $tipo_dte,
                'numero_control' => $numeroControl,
                'codigo_generacion' => $codigoGeneracion,
                'fecha_emision' => now()->format('Y-m-d'),
                'cliente_id' => $request->cliente_id,
                'empresa_id' => Auth::user()->empresa_id,
                'estado' => 'firmado',
                'json_dte' => json_encode($jsonDTE['dteJson'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);

            $sale = Sales::create([
                'cliente_id' => $request->cliente_id,
                'user_id' => Auth::id(),
                'fecha_venta' => Carbon::now(),
                'total' => $request->total,
                'cambio' => floatval($request->cambio),
                'status' => 'PAID',
                'tipo_pago' => $tipo_pago,
                'tipo_venta' => $request->tipo_venta,
                'plazos' => $request->plazos,
                'tipo_plazo' => $request->tipo_plazo,
                'abono' => $request->abono,
                'saldo_pendiente' => $request->saldo_pendiente,
                'observaciones' => $request->observaciones ?? '',
                'monto_efectivo' => $request->monto_efectivo ?? 0,
                'monto_transferencia' => $request->monto_transferencia ?? 0,
                'cuenta_bancaria_id' => $request->cuenta_bancaria_id ?? null,
                'documento_dte_id' => $documentoDte->id,
            ]);

            if ($tipo_pago === '04') {
                $cheque = $sale->cheque()->create([
                    'cliente_id' => $request->cliente_id,
                    'numero_cheque' => $request->numero_cheque,
                    'cuenta_bancaria_id' => $request->cuenta_bancaria_id,
                    'fecha_emision' => $request->fecha_emision,
                    'fecha_pago' => now(),
                    'monto' => $request->monto ?? $request->total,
                    'estado' => $request->estado,
                    'observaciones' => $request->observaciones,
                    'correlativo' => $request->correlativo,
                ]);
                $sale->cheque_bancario_id = $cheque->id;
                $sale->save();
            }

            // Guardar detalles y modificar stock
            foreach ($request->producto_id as $index => $productoId) {
                $cantidad = $request->cantidad[$index];

                SalesDetails::create([
                    'sale_id' => $sale->id,
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $request->precio_unitario[$index],
                    'sub_total' => $request->sub_total[$index],
                    'descuento_porcentaje' => $request->descuento_porcentaje[$index] ?? null,
                    'descuento_en_dolar' => $request->descuento_en_dolar[$index] ?? null,
                ]);

                $producto = Producto::find($productoId);
                $producto->save();
            }

            DB::commit();

            $pdf = PDF::loadView('postSales.ticket', [
                'venta' => $sale->load('clientes', 'detalles.producto'),
            ]);

            return response()->json([
                'mensaje' => 'Venta creada y documento electrónico generado',
                'pdf_base64' => base64_encode($pdf->output()),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al generar la venta: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function generarCotizacion(Request $request)
    {
        /**
         * 
         * Logica para trabajar cotizaciones
         */
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_pago' => 'required|string',
            'total' => 'required|numeric|min:0',
            'producto_id' => 'required|array',
            'producto_id.*' => 'exists:productos,id',
            'cantidad.*' => 'required|integer|min:1',
            'precio_unitario.*' => 'required|numeric|min:0',
            'cambio.*' => 'required|numeric|min:0',
            'sub_total.*' => 'required|numeric|min:0',
            'descuento_porcentaje.*' => 'nullable|numeric|min:0|max:100',
            'descuento_en_dolar.*' => 'nullable|numeric|min:0',
        ]);


        /**
         * logica para generar cotizacion
         */


        DB::beginTransaction();
        try {
            $cotizacion = Sales::create([
                'cliente_id'   => $request->cliente_id,
                'user_id'      => Auth::id(),
                'fecha_venta'  => Carbon::now(),
                'total'        => $request->total,
                'status'       => 'PENDING',
                'tipo_pago'    => $request->tipo_pago,
                'observaciones' => $request->observaciones ?? '',
            ]);


            foreach ($request->producto_id  as $index => $productoId) {

                $cantidad = $request->cantidad[$index];
                $cambio = floatval($request->cambio);

                SalesDetails::create([
                    'sale_id'        => $cotizacion->id,
                    'producto_id'    => $productoId,
                    'cantidad'       => $cantidad,
                    'precio_unitario' => $request->precio_unitario[$index],
                    'sub_total'      => $request->sub_total[$index],
                    'cambio'         => $cambio,
                    'descuento_porcentaje' => $request->descuento_porcentaje[$index] ?? null,
                    'descuento_en_dolar'  => $request->descuento_en_dolar[$index] ?? null,
                ]);
            }

            DB::commit();

            $pdf = Pdf::loadView('postSales.cotizacion', [
                'venta' => $cotizacion->load('clientes', 'detalles.producto'),
            ]);

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="cotizacion.pdf"');

            return response()->json([
                'success' => 'Cotizacion registrada correctamente.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);
        }
    }


    public function ventasDays()
    {
        return view('salesday.index');
    }

    public function ventasDelDia(Request $request)
    {
        if ($request->ajax()) {
            $data = Sales::getSalesdelDia();
            return DataTables::of($data)
                ->addColumn('cliente', function ($data) {
                    return $data?->clientes?->nombre ?? 'sin data';
                })
                ->addColumn('usuario', function ($data) {
                    return $data?->users?->name ?? 'sin data';
                })
                ->addColumn('fecha_venta', function ($data) {
                    return $data?->fecha_venta ?? 'sin data';
                })
                ->addColumn('tipo_pago', function ($data) {
                    return $data?->tipo_pago ?? 'sin data';
                })
                ->addColumn('status', function ($data) {
                    return $data?->status ?? 'sin data';
                })
                ->addColumn('total', function ($data) {
                    return number_format($data?->total, 2) ?? 'sin data';
                })
                ->addColumn('acciones', function ($data) {
                    $viewsalesdetails =
                        '<a href="#" 
                        class="btn btn-success mt-mobile w-90 mx-2 btn-show-details"
                        data-bs-toggle="modal"
                        data-bs-target="#verSale"
                        data-id="' . $data->id . '"
                        title="Ver detalles de esta venta">
                        <i class="bx bx-show"></i>
                    </a>';
                    $imprimir = '<a href=" ' . route('sales.generarPDfDetalles', $data->id) . ' " 
                                    class="btn btn-dark mt-mobile w-90 mx-2"
                                    title="Imprimir" target="_blank">
                                    <i class="bx bx-printer"></i>
                             </a>';

                    return $viewsalesdetails . $imprimir;
                })
                ->rawColumns(['acciones'])
                ->make(true);
        }
    }


    public function verDetallesdeVenta($id)
    {
        $sale = Sales::with([
            'clientes:id,nombre',
            'users:id,name',
            'detalles.producto:id,nombre,codigo'
        ])->find($id);

        if (!$sale) {
            return response()->json(['error' => 'No se encontró el detalle de esta venta'], 422);
        }

        return response()->json([
            'venta' => $sale
        ]);
    }

    public function generarPDfDetalles($id)
    {
        $sale = Sales::with([
            'clientes:id,nombre',
            'users:id,name',
            'detalles.producto:id,nombre,codigo'
        ])->find($id);


        if (!$sale) {
            return response()->json(['error' => 'No se encontro este documento'], 422);
        }

        $pdf = Pdf::loadView('salesday.pdfSalesdays', compact('sale'));

        return $pdf->stream('salesday.reportedeVenta');

        return response()->json(['success' => 'El pdf se genero correctamente'], 200);
    }
}
