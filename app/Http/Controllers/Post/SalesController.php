<?php

namespace App\Http\Controllers\Post;

use App\Exports\VentasMensualesExport;
use App\Helpers\NumeroALetras as HelpersNumeroALetras;
use App\Http\Controllers\Controller;
use App\Mail\EnviarDTECliente;
use App\Models\Bancos\Bancos;
use App\Models\Bancos\ChequeRecibido;
use App\Models\Bancos\CuentasBancarias;
use App\Models\CorrelativoDte;
use App\Models\CXC\CuentasPorCobrar;
use App\Models\DGII\DocumentosDte;
use App\Models\Producto\Producto;
use App\Models\SociosNegocios\Clientes;
use App\Models\SociosNegocios\Empresa;
use App\Models\Ventas\Sales;
use App\Models\Ventas\SalesDetails;
use App\Services\DteGeneratorService;
use App\Services\DteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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

    /* procedemos a armar el dte */
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
            "motivoContin" => null,
            "fecEmi" => (string)$fecha,
            "horEmi" => (string)$hora,
            "tipoMoneda" => "USD"
        ];

        if ($tipoDte === '15') {
            unset($identificacion['tipoContingencia'], $identificacion['motivoContin']);
        }


        return $identificacion;
    }

    public static function obtenerEmisor(
        array $empresa,
        string $tipo_dte,
        string $nrc,
        string $nit,
        string $codActividad,
        string $descActividad,
        ?string $nombreComercial,
        string $tipoEstablecimiento,
        string $complemento,
        string $telefono,
        string $correo,
    ): array {
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

        if (!empty($nombreComercial) && $tipo_dte !== '14') {
            $emisor['nombreComercial'] = $nombreComercial;
        }

        if ($tipo_dte == "01" || $tipo_dte == "03") {
            $emisor['nombreComercial'] = (string)$nombreComercial;
            $emisor['tipoEstablecimiento'] = (string)$tipoEstablecimiento;
            $emisor['codEstableMH'] = $empresa['codEstablecimientoMH'] ?? null;
            $emisor['codEstable'] = $empresa['codEstablecimientoMH'] ?? null;
            $emisor['codPuntoVentaMH'] = !empty($empresa['codPuntoVentaMH']) ? $empresa['codPuntoVentaMH'] : null;
            $emisor['codPuntoVenta'] = $empresa['codPuntoVentaMH'] ?? null;
        } elseif ($tipo_dte == "05" || $tipo_dte == "06") {
            $emisor['nombreComercial'] = (string)$nombreComercial;
            $emisor['tipoEstablecimiento'] = (string)$tipoEstablecimiento;
        } elseif ($tipo_dte == "14") {
            $emisor['codEstableMH'] = $empresa['codEstablecimientoMH'] ?? null;
            $emisor['codEstable'] = $empresa['codEstablecimientoMH'] ?? null;
            $emisor['codPuntoVentaMH'] = !empty($empresa['codPuntoVentaMH']) ? $empresa['codPuntoVentaMH'] : null;
            $emisor['codPuntoVenta'] = $empresa['codPuntoVentaMH'] ?? null;
        } elseif ($tipo_dte  == '15') {
            $emisor['nombreComercial'] = (string)$nombreComercial;
            $emisor['tipoEstablecimiento'] = (string)$tipoEstablecimiento;
            $emisor['tipoDocumento'] = $empresa['tipo_documento'] ?? null;
            $emisor['numDocumento'] = $empresa['nit'] ?? null;
            $emisor['codEstableMH'] = $empresa['codEstablecimientoMH'] ?? null;
            $emisor['codEstable'] = $empresa['codEstablecimientoMH'] ?? null;
            $emisor['codPuntoVentaMH'] = !empty($empresa['codPuntoVentaMH']) ? $empresa['codPuntoVentaMH'] : null;
            $emisor['codPuntoVenta'] = $empresa['codPuntoVentaMH'] ?? null;
        } else {
            return [];
        }

        return ['emisor' => $emisor];
    }


    public static function getReceptor($tipo_dte, $receptor)
    {
        $nombre = trim($receptor['nombre']);
        $nombreComercial = isset($receptor['nombreComercial']) ? trim($receptor['nombreComercial']) : '';
        $tipo_documento = isset($receptor['tipo_documento']) ? (string)$receptor['tipo_documento'] : null;
        $nrc = isset($receptor['nrc']) ? (string)str_replace('-', '', $receptor['nrc']) : null;
        $nit = isset($receptor['nit']) ? (string)str_replace('-', '', $receptor['nit']) : null;
        $numDocumento = isset($receptor['numDocumento']) ? (string)$receptor['numDocumento'] : null;
        $codActividad = isset($receptor['codActividad']) ? (string)$receptor['codActividad'] : null;
        $descActividad = isset($receptor['descActividad']) ? (string)$receptor['descActividad'] : null;

        $departamento = isset($receptor['departamento']) ? (string)$receptor['departamento'] : null;
        $municipio = isset($receptor['municipio']) ? (string)$receptor['municipio'] : null;
        $complemento = isset($receptor['direccion']) ? (string)$receptor['direccion'] : null;
        $telefono = isset($receptor['telefono']) ? (string)str_replace('-', '', $receptor['telefono']) : null;
        $correo = isset($receptor['correo']) ? (string)$receptor['correo'] : null;
        $codDomiciliado = isset($receptor['codDomiciliado']) ? (int)$receptor['codDomiciliado'] : null;
        $codPais = isset($receptor['codPais']) ? (string)$receptor['codPais'] : null;

        $dataReceptorDte = [];

        switch ($tipo_dte) {
            case "01":
                $dataReceptorDte = [
                    "tipoDocumento" => (string)$tipo_documento,
                    "numDocumento" => $numDocumento,
                    "nrc" => null,
                    "nombre" => $nombre,
                    "codActividad" => $codActividad,
                    "descActividad" => $descActividad,
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
                    "nit" => $nit,
                    "nrc" => $nrc,
                    "nombre" => $nombre,
                    "codActividad" => $codActividad,
                    "descActividad" => $descActividad,
                    "nombreComercial" => $nombreComercial,
                    "direccion" => [
                        "departamento" => $departamento,
                        "municipio" => $municipio,
                        "complemento" => $complemento
                    ],
                    "telefono" => (string)$telefono,
                    "correo" => (string)$correo
                ];
                break;

            case "14":
                $dataReceptorDte = [
                    "tipoDocumento" => (string)$tipo_documento,
                    "numDocumento" => $numDocumento,
                    "nombre" => $nombre,
                    "codActividad" => $codActividad,
                    "descActividad" => $descActividad,
                    "direccion" => [
                        "departamento" => $departamento,
                        "municipio" => $municipio,
                        "complemento" => $complemento
                    ],
                    "telefono" => (string)$telefono,
                    "correo" => (string)$correo
                ];
                break;

            case "15":
                $dataReceptorDte = [
                    "tipoDocumento" => (string)$tipo_documento,
                    "numDocumento" => $numDocumento,
                    "nrc" => $nrc,
                    "nombre" => $nombre,
                    "codActividad" => $codActividad,
                    "descActividad" => $descActividad,
                    "direccion" => [
                        "departamento" => $departamento,
                        "municipio" => $municipio,
                        "complemento" => $complemento
                    ],
                    "telefono" => (string)$telefono,
                    "correo" => (string)$correo,
                    "codDomiciliado" => (int)$codDomiciliado,
                    "codPais" => (string)$codPais
                ];
                break;
            default:
                $dataReceptorDte = [];
                break;
        }
        return $dataReceptorDte;
    }


    /* generamos el numero de control */
    private function generarNumeroControl(string $tipoDte, string $codigoEstablecimiento, int $correlativo): string
    {
        /* Asegurarse que el código de establecimiento tenga el formato correcto (ej. MP000001) */
        $codigoEstablecimientoFormateado = strtoupper($codigoEstablecimiento); // No lo modificamos más

        /* Asegurarse que el correlativo tenga 15 dígitos con ceros a la izquierda */
        $correlativoFormateado = str_pad((string) $correlativo, 15, '0', STR_PAD_LEFT);

        return "DTE-{$tipoDte}-{$codigoEstablecimientoFormateado}-{$correlativoFormateado}";
    }



    private function obtenerCorrelativo(string $tipoDte, string $codigoEstablecimiento): int
    {
        $registro = CorrelativoDte::firstOrCreate(
            ['tipo_dte' => $tipoDte, 'codigo_establecimiento' => $codigoEstablecimiento],
            ['correlativo' => 1]
        );

        $correlativoActual = $registro->correlativo;
        /**
         * Incrementamos para el siguiente uso
         */
        $registro->increment('correlativo');

        return $correlativoActual;
    }

    /* resumen de ventas */
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
            "iva" => $iva,                        // IVA 13%
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

    /* generamos el body del documento */
    protected function generarBodyCCF(Request $request): array
    {
        $productos = [];
        $sumas = 0.00;
        $descuentoTotal = 0.00;
        $porcentajeIVA = 0.13;

        foreach ($request->producto_id as $index => $productoId) {
            $cantidad = (int) $request->cantidad[$index];
            $precioUnitario = (float) $request->precio_unitario[$index];
            $descuento = isset($request->descuento_en_dolar[$index]) ? (float) $request->descuento_en_dolar[$index] : 0.00;

            $ventaGravada = round(($cantidad * $precioUnitario) - $descuento, 2);

            $sumas += $ventaGravada;
            $descuentoTotal += $descuento;

            $producto = Producto::find($productoId);

            $productoItem = [
                "numItem" => $index + 1,
                "tipoItem" => (int)$producto->items->codigo,
                "cantidad" => $cantidad,
                "codigo" => (string) $producto->codigo,
                "codTributo" => null,
                "numeroDocumento" => null,
                "uniMedida" => (int) $producto->unidad->codigo,
                "descripcion" => $producto->nombre,
                "precioUni" => round($precioUnitario, 2),
                "montoDescu" => round($descuento, 2),
                "ventaNoSuj" => 0.00,
                "ventaExenta" => 0.00,
                "ventaGravada" => $ventaGravada,
                "psv" => 0.00,
                "noGravado" => 0.00,
                "tributos" => ["20"]
            ];

            $productos[] = $productoItem;
        }

        $ivaTotal = round($sumas * $porcentajeIVA, 2);

        /**
         * Obtenemos el cliente
         */
        $cliente = Clientes::find($request->cliente_id);

        /**
         * Total bruto antes de retención
         */
        $montoTotalOperacion = round($sumas + $ivaTotal, 2);

        /**
         * Aplicar retención solo si es gran contribuyente (retención sobre el monto sin IVA)
         */
        $ivaRetenido = ($cliente && $cliente->tipo_contribuyente === 'gran_contribuyente') ? round($sumas * 0.01, 2) : 0.00;

        /**
         * Total a pagar ya con retención aplicada
         */
        $total = round($montoTotalOperacion - $ivaRetenido, 2);

        $tributos = [
            [
                "codigo" => "20",
                "descripcion" => "Impuesto al Valor Agregado 13%",
                "valor" => $ivaTotal
            ]
        ];

        $resumen = [
            "totalNoSuj" => 0.00,
            "totalExenta" => 0.00,
            "totalGravada" => round($sumas, 2),
            "totalNoGravado" => 0.00,
            "descuNoSuj" => 0.00,
            "descuExenta" => 0.00,
            "descuGravada" => round($descuentoTotal, 2),
            "totalDescu" => round($descuentoTotal, 2),
            "porcentajeDescuento" => 0.00,
            "subTotal" => round($sumas, 2),
            "ivaRete1" => $ivaRetenido,
            "ivaPerci1" => 0.00,
            "reteRenta" => 0.00,
            "subTotalVentas" => round($sumas, 2),
            "tributos" => $tributos,
            "montoTotalOperacion" => $montoTotalOperacion, /* Aquí no se resta la retención */
            "totalPagar" => $total,
            "saldoFavor" => 0.00,
            "totalLetras" => HelpersNumeroALetras::convertir($total, 'DÓLARES'),
            "condicionOperacion" => (int)$request->tipo_venta,
            "numPagoElectronico" => "",
            "pagos" => (int)$request->tipo_venta === 2 ? [
                [
                    "codigo" => "02",
                    "montoPago" => $total,
                    "plazo" => $request->plazo ?? null,
                    "referencia" => $request->referencia ?? "",
                    "periodo" => !empty($request->periodo) ? (int)$request->periodo : null,
                ]
            ] : null,
        ];

        return [
            "productos" => $productos,
            "resumen" => $resumen
        ];
    }

    protected function generarBodyFactura(Request $request): array
    {
        $productos = [];
        $totalVentaGravada = 0.0;
        $totalDescuento = 0.0;
        $totalIva = 0.0;
        $porcentajeIVA = 0.13;
        $totalVentaSinIVA = 0.0; /* Acumulador para la base sin IVA (para calcular la retención) */

        foreach ($request->producto_id as $index => $productoId) {
            $cantidad = (int) $request->cantidad[$index];
            $precioUnitarioSinIVA = (float) $request->precio_unitario[$index];

            /* Precio con IVA */
            $precioUnitarioConIVA = round($precioUnitarioSinIVA * (1 + $porcentajeIVA), 4);

            $descuento = isset($request->descuento_en_dolar[$index]) ? (float) $request->descuento_en_dolar[$index] : 0.0;

            $producto = Producto::find($productoId);

            $ventaGravada = ($precioUnitarioConIVA * $cantidad) - $descuento;

            $base = $ventaGravada / (1 + $porcentajeIVA);
            $ivaItem = $ventaGravada - $base;

            $totalVentaGravada += round($ventaGravada, 3);
            $totalVentaSinIVA += round(($precioUnitarioSinIVA * $cantidad) - $descuento, 3);
            $totalDescuento += round($descuento, 3);
            $totalIva += round($ivaItem, 2);

            $productos[] = [
                "numItem" => $index + 1,
                "tipoItem" => (int)$producto->items->codigo,
                "numeroDocumento" => null,
                "cantidad" => $cantidad,
                "codigo" => $producto->codigo ?? null,
                "codTributo" => null,
                "uniMedida" => (int)$producto->unidad->codigo,
                "descripcion" => $producto->nombre,
                "precioUni" => round($precioUnitarioConIVA, 2),
                "montoDescu" => round($descuento, 3),
                "ventaNoSuj" => 0,
                "ventaExenta" => 0,
                "ventaGravada" => round($ventaGravada, 3),
                "tributos" => null,
                "psv" => 0,
                "noGravado" => 0,
                "ivaItem" => round($ivaItem, 2),
            ];
        }

        $montoOperacion = round($totalVentaGravada, 2);
        $ivaTotal = round($totalIva, 2);

        $cliente = Clientes::find($request->cliente_id);

        /**
         * Solo si es gran contribuyente aplicamos la retención del 1% sobre la base sin IVA
         */
        if ($cliente && $cliente->tipo_contribuyente === 'gran_contribuyente') {
            $ivaRete1 = round($totalVentaSinIVA * 0.01, 2);
        } else {
            $ivaRete1 = 0.00;
        }

        /**
         * Total a pagar ya con la retención aplicada
         */
        $totalPagar = round($montoOperacion - $ivaRete1, 2);

        $resumen = [
            "totalNoSuj" => 0,
            "totalExenta" => 0,
            "totalGravada" => $montoOperacion,
            "subTotalVentas" => $montoOperacion,
            "descuNoSuj" => 0,
            "descuExenta" => 0,
            "descuGravada" => 0,
            "porcentajeDescuento" => 0,
            "totalDescu" => round($totalDescuento, 2),
            "tributos" => [],
            "subTotal" => $montoOperacion,
            "ivaRete1" => $ivaRete1,
            "reteRenta" => 0,
            "montoTotalOperacion" => $montoOperacion,
            "totalNoGravado" => 0,
            "totalPagar" => $totalPagar,
            "totalLetras" => HelpersNumeroALetras::convertir($totalPagar, 'DÓLARES'),
            "totalIva" => $ivaTotal,
            "saldoFavor" => 0,
            "condicionOperacion" => (int)$request->tipo_venta,
            "pagos" => [
                [
                    "codigo" => "01",
                    "montoPago" => $totalPagar,
                    "plazo" => $request->plazo ?? null,
                    "referencia" => $request->referencia ?? "",
                    "periodo" => !empty($request->periodo) ? (int)$request->periodo : null,
                ]
            ],
            "numPagoElectronico" => null,
        ];

        return [
            "productos" => $productos,
            "resumen" => $resumen,
        ];
    }

    protected function generarBodyExcluido(Request $request): array
    {
        $productos = [];
        $totalDescuento = 0.0;
        $sumaItemsExacta = 0.0;

        foreach ($request->producto_id as $index => $productoId) {
            $cantidad = (int) $request->cantidad[$index];
            $precioUnitario = (float) $request->precio_unitario[$index];
            $descuento = isset($request->descuento_en_dolar[$index]) ? (float) $request->descuento_en_dolar[$index] : 0.0;

            $producto = Producto::find($productoId);
            $precioTotal = $precioUnitario * $cantidad;
            $compraSinRedondear = $precioTotal - $descuento;

            $sumaItemsExacta += $compraSinRedondear;
            $totalDescuento += $descuento;

            $productos[] = [
                "numItem" => $index + 1,
                "tipoItem" => (int) $producto->items->codigo,
                "cantidad" => $cantidad,
                "codigo" => $producto->codigo ?? null,
                "uniMedida" => (int) $producto->unidad->codigo,
                "descripcion" => $producto->nombre,
                "precioUni" => round($precioUnitario, 2),
                "montoDescu" => round($descuento, 2),
                "compra" => round($compraSinRedondear, 2)
            ];
        }

        $reteRenta = round($sumaItemsExacta * 0.10, 2);
        $totalPagar = round($sumaItemsExacta - $reteRenta, 2);

        $resumen = [
            "totalCompra" => round($sumaItemsExacta, 2),
            "descu" => round($totalDescuento, 2),
            "totalDescu" => round($totalDescuento, 2),
            "subTotal" => round($sumaItemsExacta, 2),
            "ivaRete1" => 0.00,
            "reteRenta" => $reteRenta,
            "totalPagar" => $totalPagar,
            "totalLetras" => HelpersNumeroALetras::convertir($totalPagar, 'DÓLARES'),
            "condicionOperacion" => (int) $request->tipo_venta,
            "pagos" => [
                [
                    "codigo" => "01",
                    "montoPago" => $totalPagar,
                    "plazo" => $request->plazo ?? null,
                    "referencia" => $request->referencia ?? "",
                    "periodo" => !empty($request->periodo) ? (int) $request->periodo : null,
                ]
            ],
            "observaciones" => $request->observaciones ?? 'Sujeto Excluido'
        ];

        return [
            "productos" => $productos,
            "resumen" => $resumen,
        ];
    }

    protected function generarBodyComprobanteDonacion(Request $request): array
    {
        $cuerpoDocumento = [];
        $valorTotal = 0.0;

        foreach ($request->producto_id as $index => $productoId) {
            $cantidad = (int) $request->cantidad[$index];
            $valorUnitario = (float) $request->precio_unitario[$index];

            $producto = Producto::find($productoId);
            $valor = $cantidad * $valorUnitario;
            $valorTotal += $valor;

            $cuerpoDocumento[] = [
                "numItem" => $index + 1,
                "tipoDonacion" => (int) $producto->items->codigo,
                "cantidad" => $cantidad,
                "codigo" => $producto->codigo ?? null,
                "uniMedida" => (int) $producto->unidad->codigo,
                "descripcion" => $producto->nombre,
                "valorUni" => round($valorUnitario, 2),
                "valor" => round($valor, 2),
                "depreciacion" => 0.00
            ];
        }

        $resumen = [
            "valorTotal" => round($valorTotal, 2),
            "totalLetras" => HelpersNumeroALetras::convertir($valorTotal, 'DÓLARES'),
            "pagos" => [
                [
                    "codigo" => "01",
                    "montoPago" => $valorTotal,
                    "referencia" => $request->referencia ?? "",
                ]
            ],
        ];

        return [
            "productos" => $cuerpoDocumento,
            "resumen" => $resumen,
            "apendice" => null
        ];
    }

    public function generarBodyDocumento(Request $request): array
    {
        $tipoDocumento = $request->tipo_documento;

        if ($tipoDocumento === 'ccf') {
            return $this->generarBodyCCF($request);
        }

        if ($tipoDocumento === 'factura') {
            return $this->generarBodyFactura($request);
        }

        if ($tipoDocumento === 'factura_sujeto_excluido') {
            return $this->generarBodyExcluido($request);
        }
        if ($tipoDocumento === 'comprobante_donacion') {
            return $this->generarBodyComprobanteDonacion($request);
        }

        /**
         * Por defecto si no es ninguno, retornar arreglo vacío o error
         */
        return [
            "productos" => [],
            "resumen" => []
        ];
    }

    /* funcion para guardar la venta y emitir el dte */
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
            'cuenta_bancaria_id' => 'required_if:tipo_pago,cheque,transferencia|exists:cuentas_bancarias,id',
            'numero_cheque' => 'required_if:tipo_pago,cheque|string',
            'fecha_emision' => 'required_if:tipo_pago,cheque|date',
            'cambio' => 'required_if:tipo_venta,1|numeric|min:0',
            'estado' => 'nullable|string',
            'observaciones' => 'nullable|string',
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
            $empresa = Auth::user()->empresa->load('actividad', 'departamento', 'municipio');
            $codigoEstablecimiento = $empresa->codEstablecimientoMH . $empresa->codPuntoVentaMH;
            $correlativo = $this->obtenerCorrelativo($tipo_dte, $codigoEstablecimiento);
            $numeroControl = $this->generarNumeroControl($tipo_dte, $codigoEstablecimiento, $correlativo);
            $ambiente = $empresa->ambiente ?? '00';

            switch ($tipo_dte) {
                case '03':
                    $version = 3;
                    break;
                case '01':
                case '14':
                case '15':
                default:
                    $version = 1;
                    break;
            }
            $identificacion = self::identificacionGet(
                $version,
                $tipo_dte,
                $ambiente,
                $codigoGeneracion,
                $fecha,
                $hora,
                $numeroControl,
                null
            );
            $cliente = Clientes::findOrFail($request->cliente_id);

            $receptor = self::getReceptor($tipo_dte, array_merge([
                'nombre' => $cliente->nombre,
                'nombreComercial' => $tipo_dte === '14' ? null : $cliente->nombreComercial,
                'tipo_documento' => $cliente->tipo_documento,
                'numDocumento' => $cliente->numero_documento,
                'nit' => $cliente->nit,
                'nrc' => $cliente->nrc,
                'codActividad' => $cliente->actividad->codActividad,
                'descActividad' => $cliente->actividad->descActividad,
                'departamento' => $cliente->departamento->codigo,
                'municipio' => $cliente->municipio->codigo,
                'direccion' => $cliente->direccion,
                'telefono' => $cliente->telefono,
                'correo' => $cliente->correo_electronico,
            ], $tipo_dte === '15' ? [
                'codDomiciliado' => $cliente->codDomiciliado ?? null,
                'codPais' => $cliente->codPais ?? 'SV',
            ] : []));
            $emisorData = self::obtenerEmisor(
                $empresa->toArray(),
                $tipo_dte,
                $empresa->nrc,
                $empresa->nit,
                $empresa->actividad->codActividad,
                $empresa->actividad->descActividad,
                $tipo_dte === '14' ? null : $empresa->nombreComercial,
                $empresa->tipoEstablecimiento ?? '01',
                $empresa->complemento ?? '',
                $empresa->telefono ?? '',
                $empresa->correo ?? ''
            );

            if ($tipo_dte === '14') {
                unset($emisorData['emisor']['nombreComercial']);
            }
            if ($tipo_dte === '15') {
                $emisor = $emisorData['emisor'];
                unset($emisor['nit']);
                /* Insertamos al principio con + operador para controlar el orden */
                $emisorData['emisor'] = [
                    'tipoDocumento' => $empresa->tipo_documento,
                    'numDocumento'  => $empresa->num_documento ?? $empresa->nit,
                ] + $emisor;
            }

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
                    "sujetoExcluido" => isset($receptor['sujetoExcluido']) ? $receptor['sujetoExcluido'] : $receptor,
                    "cuerpoDocumento" => $cuerpoDocumento,
                    "resumen" => $bodyDocumento['resumen'],
                    "apendice" => null,
                ];
            } elseif ($tipo_dte === '15') {
                $jsonDTE['dteJson'] = [
                    "identificacion" => $identificacion,
                    "donatario" => $emisorData['emisor'],
                    "donante" => isset($receptor['receptor']) ? $receptor['receptor'] : $receptor,
                    "otrosDocumentos" => [
                        [
                            "codDocAsociado" => (int)$cliente->codDomiciliado ?? null,
                            "descDocumento" => $request->descDocumento ?? null,
                            "detalleDocumento" => $request->detalleDocumento ?? null
                        ]
                    ],
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

            /**
             * Guardamos la venta
             */

            $estadoVenta = $request->tipo_venta == 1 ? 'PAID' : 'PENDING';

            $sale = Sales::create([
                'cliente_id' => $request->cliente_id,
                'user_id' => Auth::id(),
                'fecha_venta' => Carbon::now(),
                'total' => $request->total,
                'cambio' => floatval($request->cambio),
                'status' => $estadoVenta,
                'tipo_pago' => $tipo_pago,
                'tipo_venta' => $request->tipo_venta,
                "plazo" => $request->plazo ?? null,
                "referencia" => $request->referencia ?? "",
                "periodo" => $request->periodo ?? null,
                "iva" => $tipo_dte === '03'
                    ? (collect($bodyDocumento['resumen']['tributos'] ?? [])
                        ->firstWhere('codigo', '20')['valor'] ?? 0)
                    : ($bodyDocumento['resumen']['totalIva'] ?? 0),
                'retencion' => match ($tipo_dte) {
                    '14' => $bodyDocumento['resumen']['reteRenta'] ?? 0,
                    '01', '03' => $bodyDocumento['resumen']['ivaRete1'] ?? 0,
                    default => 0,
                },
                'observaciones' => $request->observaciones ?? null,
                'descDocumento' => $request->descDocumento ?? null,
                'detalleDocumento' => $request->detalleDocumento ?? null,
                'monto_efectivo' => $request->monto_efectivo ?? 0,
                'monto_transferencia' => $request->monto_transferencia ?? 0,
                'cuenta_bancaria_id' => $request->cuenta_bancaria_id ?? null,
                'documento_dte_id' => $documentoDte->id,
            ]);

            if ($request->tipo_venta == 1) {
                CuentasPorCobrar::create([
                    'sale_id' => $sale->id,
                    'monto' => $request->total,
                    'saldo_pendiente' => 0, 
                    'fecha_pago' => now(),
                    'metodo_pago' => $request->tipo_pago,
                ]);
            } else if ($request->tipo_venta == 2) {
                CuentasPorCobrar::create([
                    'sale_id' => $sale->id,
                    'monto' => $request->total,
                    'saldo_pendiente' => $request->total, 
                    'fecha_pago' => null,
                    'metodo_pago' => null,
                ]);
            }



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

            /**
             * Guardar detalles y modificar stock 
             */
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

            $token = $empresa->token;
            if (!$token || Carbon::now()->greaterThan($empresa->token_expire)) {
                $token = DteService::loginMH($empresa);
            }

            $xmlFirmado = DteService::firmarDTE($jsonDTE['dteJson'], $empresa);
            $mhResponse = DteService::enviarDTE($xmlFirmado, $empresa, $tipo_dte, $codigoGeneracion, $ambiente);

            $documentoDte->update([
                'sello_recibido' => $mhResponse['selloRecibido'] ?? null,
                'mh_response' => json_encode($mhResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);

            /**
             * Agregar firmaElectronica dentro del JSON del DTE
             */
            $jsonDTE['dteJson']['firmaElectronica'] = [
                'ambiente' => $ambiente,
                'version' => $version,
                'tipoDte' => $tipo_dte,
                'codigoGeneracion' => $codigoGeneracion,
                'firmaElectronica' => $mhResponse['firmaElectronica'] ?? null,
                'selloRecibido' => $mhResponse['selloRecibido'] ?? null,
            ];

            /**
             * Actualizar el documento DTE con la firma electrónica agregada
             */
            $documentoDte->update([
                'json_dte' => json_encode($jsonDTE['dteJson'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ]);


            /**
             * enviamos el tipo de dte generado al correo del cliente.
             */
            $view = match ($tipo_dte) {
                '01' => 'documentos.pdf.fe',
                '03' => 'documentos.pdf.ccf',
                '14' => 'documentos.pdf.se',
                '15' => 'documentos.pdf.cd',
                default => abort(404, 'Tipo de documento no soportado.')
            };

            $json = json_decode($documentoDte->json_dte, true);
            $mh = json_decode($documentoDte->mh_response ?? '{}', true);

            $pdf = PDF::loadView($view, [
                'venta' => $sale->load('clientes', 'detalles.producto'),
                'documento' => $documentoDte,
                'json' => $json,
                'mh' => $mh,
            ]);

            $pdfContent = $pdf->output();
            $codigoGeneracion = $json['identificacion']['codigoGeneracion'] ?? 'sin_codigo';
            $jsonContent = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            if ($cliente->correo_electronico) {
                Mail::to($cliente->correo_electronico)->send(
                    new EnviarDTECliente($sale, $pdfContent, $codigoGeneracion, $jsonContent)
                );
            }


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


    public function SalesIndex()
    {
        $clientes = Clientes::all();
        return view('sales.salesIndex', compact('clientes'));
    }

    public function SalesIndexGetData(Request $request)
    {
        if ($request->ajax()) {
            $data = Sales::getSalesDataTotal(
                $request->cliente_id,
                $request->fecha_inicio,
                $request->fecha_fin
            );
            return DataTables::of($data)
                ->addColumn('documento_relacionado', function ($data) {
                    if ($data?->documentoDte) {
                        $tipo = $data->documentoDte->tipo_documento;
                        $control = $data->documentoDte->numero_control;

                        return '<span style="
                                display: inline-block;
                                background-color: #f1f1f1;
                                border: 1px solid #ccc;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 13px;
                                color: #333;
                                margin: 2px 0;
                                ">' . e($tipo) . ' <span style="margin: 0 4px;">#</span> ' . e($control) . '</span>';
                    }

                    return '<span style="
                                display: inline-block;
                                background-color: #eee;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 13px;
                                color: #888;
                                ">Sin dato</span>';
                })
                ->addColumn('cliente', function ($data) {
                    return $data?->clientes?->nombre ?? 'sin data';
                })
                ->addColumn('usuario', function ($data) {
                    return $data?->users?->name ?? 'sin data';
                })
                ->addColumn('fecha_venta', function ($data) {
                    if (!$data?->fecha_venta) return 'sin data';

                    Carbon::setLocale('es');
                    $fecha = Carbon::parse($data->fecha_venta);
                    return $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s h:i A');
                })
                ->addColumn('tipo_pago', function ($data) {
                    if ($data->tipo_pago == '01') {
                        return '<span class="badge badge-center rounded-pill bg-label-primary me-1"><i class="icon-base bx bx-money"></i></span> Efectivo';
                    } elseif ($data->tipo_pago == '04') {
                        return '<span class="badge badge-center rounded-pill bg-label-success me-1"><i class="icon-base bx bx-receipt"></i></span> Cheque';
                    } elseif ($data->tipo_pago == '05') {
                        return '<span class="badge badge-center rounded-pill bg-label-danger me-1"><i class="icon-base bx bx-transfer"></i></span> Transferencia';
                    } else {
                        return '<span class="badge badge-center rounded-pill bg-label-secondary me-1"><i class="icon-base bx bx-help-circle"></i></span> Otro';
                    }
                })
                ->addColumn('status', function ($data) {
                    switch ($data->status) {
                        case 'PAID':
                            return '<span class="badge badge-center rounded-pill bg-label-success me-1">
                        <i class="icon-base bx bx-check-circle"></i>
                    </span> Pagado';
                        case 'PENDING':
                            return '<span class="badge badge-center rounded-pill bg-label-warning me-1">
                        <i class="icon-base bx bx-time-five"></i>
                    </span> Pendiente';
                        case 'CANCEL':
                            return '<span class="badge badge-center rounded-pill bg-label-danger me-1">
                        <i class="icon-base bx bx-x-circle"></i>
                    </span> Cancelado';
                        default:
                            return '<span class="badge badge-center rounded-pill bg-label-secondary me-1">
                        <i class="icon-base bx bx-help-circle"></i>
                    </span> Desconocido';
                    }
                })
                ->addColumn('total', function ($data) {
                    $total = $data->total ?? 0;

                    if (isset($data->retencion) && $data->retencion > 0) {
                        $total -= $data->retencion;
                    }

                    return '$' . number_format($total, 2);
                })
                ->addColumn('acciones', function ($data) {
                    $imprimir = '';
                    $viewsalesdetails = '';

                    if (Auth()->user()->can('ventas_view_details')) {
                        $viewsalesdetails = '<a href="#" 
                                                class="mx-1 btn btn-sm bg-label-success btn-show-details"
                                                data-bs-toggle="modal"
                                                data-bs-target="#verSale"
                                                data-id="' . $data->id . '"
                                                title="Ver detalles de esta venta">
                                                <i class="bx bx-show" style="font-size: 22px;"></i>
                                         </a>';
                    }

                    if (Auth()->user()->can('ventas_print')) {
                        $imprimir = '<a href="' . route('sales.generarPDfDetalles', $data->id) . '" 
                                    class="mx-1 btn btn-sm bg-label-dark"
                                    title="Imprimir" target="_blank">
                                    <i class="bx bx-printer" style="font-size: 22px;"></i>
                                </a>';
                    }


                    $generarFactura = '';

                    if (Auth()->user()->can('ventas_send_contingencia')) {
                        if (
                            $data?->documentoDte?->tipo_documento !== '15' &&
                            $data?->documentoDte?->estado !== 'RECIBIDO' &&
                            $data?->documentoDte?->estado !== 'ANULADO' &&
                            (
                                $data?->documentoDte?->estado !== 'FIRMADO' ||
                                ($data?->documentoDte?->estado === 'FIRMADO' && $data?->documentoDte?->mh_response === null)
                            )
                        ) {
                            $generarFactura = '<a href="#" 
                        class="mx-1 btn btn-sm bg-label-danger btn-send-contingencia"
                        data-bs-toggle="modal"
                        data-bs-target="#sendContingencia"
                        data-id="' . $data->id . '"
                        title="Emitir DTE" target="_blank">
                        <i class="bx bx-file" style="font-size: 22px;"></i>
                    </a>';
                        }
                    }
                    return '<div class="d-flex justify-content-start text-nowrap">' . $viewsalesdetails . $imprimir . $generarFactura . '</div>';
                })
                ->rawColumns(['acciones', 'tipo_pago', 'status', 'documento_relacionado'])
                ->make(true);
        }
    }

    public function emitirEvenetodeContigencia(Request $request, $id)
    {
        $documento = DocumentosDte::findOrFail($id);
        $empresa = $documento->empresa;

        $jsonDte = json_decode($documento->json_dte, true);

        /* Actualizamos solamente los campos relacionados a contingencia */
        $jsonDte['identificacion']['tipoContingencia'] = (int) $request->tipoContingencia;
        $jsonDte['identificacion']['motivoContin'] = $request->motivoContingencia ?? '';

        /* Si se está enviando evento de contingencia, actualizamos tipoModelo a 2 */
        $jsonDte['identificacion']['tipoModelo'] = 2;

        /* Guardamos el JSON actualizado */
        $documento->json_dte = json_encode($jsonDte);

        try {
            $respuesta = DteService::enviarEventoContingencia($request, $empresa, $documento);

            $documento->update([
                'estado' => $respuesta['estado'] ?? null,
                'sello_recibido' => $respuesta['selloRecibido'] ?? null,
                'mh_response' => json_encode($respuesta),
                'json_dte' => $documento->json_dte, /* Guardamos el JSON modificado */
            ]);

            return response()->json(['success' => true, 'data' => $respuesta]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function descargarHistorialPDF(Request $request)
    {
        $clienteId = $request->input('cliente_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $cliente = null;
        if ($clienteId) {
            $cliente = Clientes::find($clienteId);
        }

        /* Obtener datos filtrados */
        $ventas = Sales::getSalesDataTotal($clienteId, $fechaInicio, $fechaFin);

        /* Aquí generas el PDF (usando por ejemplo DomPDF) */
        $pdf = PDF::loadView('sales.historialPDF', compact('ventas', 'cliente', 'fechaInicio', 'fechaFin'));

        /* Descargar PDF */
        return $pdf->stream('historial_ventas.pdf');
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
                    if (!$data?->fecha_venta) return 'sin data';

                    Carbon::setLocale('es');
                    $fecha = Carbon::parse($data->fecha_venta);
                    return $fecha->translatedFormat('l d \d\e F \d\e Y \a \l\a\s h:i A');
                })
                ->addColumn('tipo_pago', function ($data) {
                    if ($data->tipo_pago == '01') {
                        return '<span class="badge badge-center rounded-pill bg-label-primary me-1"><i class="icon-base bx bx-money"></i></span> Efectivo';
                    } elseif ($data->tipo_pago == '04') {
                        return '<span class="badge badge-center rounded-pill bg-label-success me-1"><i class="icon-base bx bx-receipt"></i></span> Cheque';
                    } elseif ($data->tipo_pago == '05') {
                        return '<span class="badge badge-center rounded-pill bg-label-danger me-1"><i class="icon-base bx bx-transfer"></i></span> Transferencia';
                    } else {
                        return '<span class="badge badge-center rounded-pill bg-label-secondary me-1"><i class="icon-base bx bx-help-circle"></i></span> Otro';
                    }
                })
                ->addColumn('status', function ($data) {
                    switch ($data->status) {
                        case 'PAID':
                            return '<span class="badge badge-center rounded-pill bg-label-success me-1">
                        <i class="icon-base bx bx-check-circle"></i>
                    </span> Pagado';
                        case 'PENDING':
                            return '<span class="badge badge-center rounded-pill bg-label-warning me-1">
                        <i class="icon-base bx bx-time-five"></i>
                    </span> Pendiente';
                        case 'CANCEL':
                            return '<span class="badge badge-center rounded-pill bg-label-danger me-1">
                        <i class="icon-base bx bx-x-circle"></i>
                    </span> Cancelado';
                        default:
                            return '<span class="badge badge-center rounded-pill bg-label-secondary me-1">
                        <i class="icon-base bx bx-help-circle"></i>
                    </span> Desconocido';
                    }
                })
                ->addColumn('total', function ($data) {
                    $monto = ($data->total ?? 0) + ($data->iva ?? 0);
                    return number_format($monto, 2);
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
                ->rawColumns(['acciones', 'tipo_pago', 'status'])
                ->make(true);
        }
    }

    public function ventasDelMes()
    {
        $clientes = Clientes::all();

        return view('saleMonth.index', compact('clientes'));
    }

    public function getDetalleVentasMensual(Request $request)
    {
        $clienteId = $request->cliente_id;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $query = Sales::with('clientes', 'users')
            ->when($clienteId, function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            })
            ->when($fechaInicio && $fechaFin, function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
            })
            ->orderBy('id', 'desc');

        return DataTables::of($query)
            ->addColumn('cliente', function ($data) {
                return $data?->clientes?->nombre ?? '';
            })
            ->addColumn('usuario', function ($data) {
                return $data?->users?->name ?? '';
            })
            ->addColumn('numero_documento', function ($data) {
                if ($data?->documentoDte) {
                    $tipo = $data->documentoDte->tipo_documento;
                    $control = $data->documentoDte->numero_control;

                    return '<span style="
                                display: inline-block;
                                background-color: #f1f1f1;
                                border: 1px solid #ccc;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 13px;
                                color: #333;
                                margin: 2px 0;
                                ">' . e($tipo) . ' <span style="margin: 0 4px;">#</span> ' . e($control) . '</span>';
                }

                return '<span style="
                                display: inline-block;
                                background-color: #eee;
                                padding: 4px 8px;
                                border-radius: 4px;
                                font-size: 13px;
                                color: #888;
                                ">Sin dato</span>';
            })
            ->addColumn('tipo_pago', function ($data) {
                if ($data->tipo_pago == '01') {
                    return '<span class="badge badge-center rounded-pill bg-label-primary me-1"><i class="icon-base bx bx-money"></i></span> Efectivo';
                } elseif ($data->tipo_pago == '04') {
                    return '<span class="badge badge-center rounded-pill bg-label-success me-1"><i class="icon-base bx bx-receipt"></i></span> Cheque';
                } elseif ($data->tipo_pago == '05') {
                    return '<span class="badge badge-center rounded-pill bg-label-danger me-1"><i class="icon-base bx bx-transfer"></i></span> Transferencia';
                } else {
                    return '<span class="badge badge-center rounded-pill bg-label-secondary me-1"><i class="icon-base bx bx-help-circle"></i></span> Otro';
                }
            })
            ->addColumn('status', function ($data) {
                switch ($data->status) {
                    case 'PAID':
                        return '<span class="badge badge-center rounded-pill bg-label-success me-1">
                        <i class="icon-base bx bx-check-circle"></i>
                    </span> Pagado';
                    case 'PENDING':
                        return '<span class="badge badge-center rounded-pill bg-label-warning me-1">
                        <i class="icon-base bx bx-time-five"></i>
                    </span> Pendiente';
                    case 'CANCEL':
                        return '<span class="badge badge-center rounded-pill bg-label-danger me-1">
                        <i class="icon-base bx bx-x-circle"></i>
                    </span> Cancelado';
                    default:
                        return '<span class="badge badge-center rounded-pill bg-label-secondary me-1">
                        <i class="icon-base bx bx-help-circle"></i>
                    </span> Desconocido';
                }
            })
            ->addColumn('total', function ($data) {
                $monto = ($data->total ?? 0) + ($data->iva ?? 0) - ($data->retencion ?? 0);
                return '$' . number_format($monto, 2);
            })
            ->rawColumns(['cliente', 'usuario', 'total', 'numero_documento', 'tipo_pago', 'status'])
            ->make(true);
    }

    public function getResumenMensual(Request $request)
    {
        $clienteId = $request->cliente_id;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $ventas = Sales::selectRaw('
                DATE_FORMAT(fecha_venta, "%Y-%m") as mes,
                IFNULL(SUM(IFNULL(total, 0) + IFNULL(iva, 0)), 0) as total_ventas
            ')
            ->when($clienteId, function ($query) use ($clienteId) {
                $query->where('cliente_id', $clienteId);
            })
            ->when($fechaInicio && $fechaFin, function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
            })
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return response()->json($ventas);
    }

    public function downloadExcelReporteMes(Request $request)
    {
        $clienteId = $request->cliente_id;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        return Excel::download(new VentasMensualesExport($clienteId, $fechaInicio, $fechaFin), 'reporte_ventas_mes.xlsx');
    }


    public function downloadReportePdfMes(Request $request)
    {
        $clienteId = $request->cliente_id;
        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $ventas = Sales::with('clientes', 'users')
            ->when($clienteId, function ($q) use ($clienteId) {
                $q->where('cliente_id', $clienteId);
            })
            ->when($fechaInicio && $fechaFin, function ($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
            })
            ->orderBy('fecha_venta', 'desc')
            ->get();

        $cliente = null;
        if ($clienteId) {
            $cliente = Clientes::find($clienteId);
        }

        $user = Auth::user();

        $pdf = Pdf::loadView('saleMonth.pdf.reporte-ventas-pdf', compact('ventas', 'cliente', 'fechaInicio', 'fechaFin', 'user'))->setPaper('A4', 'landscape');

        return $pdf->stream('reporte_ventas_mes.pdf');
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
