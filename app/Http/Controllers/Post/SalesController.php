<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Producto\Producto;
use App\Models\SociosNegocios\Clientes;
use App\Models\Ventas\Sales;
use App\Models\Ventas\SalesDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        $clientes = Clientes::all();
        return view('postSales.index', compact('productos', 'clientes'));
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


    public function generarSale(Request $request)
    {
        /**
         * aqui vamos a trabajar la logica del backend
         * 
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


        DB::beginTransaction();
        try {
            /**
             * 
             * Creamos la venta
             */
            $sale = Sales::create([
                'cliente_id'   => $request->cliente_id,
                'user_id'      => Auth::user()->id,
                'fecha_venta'  => Carbon::now(),
                'total'        => $request->total,
                'status'       => 'PAID',
                'tipo_pago'    => $request->tipo_pago,
                'observaciones' => $request->observaciones ?? '',
            ]);

            /**
             * 
             * Guardamos los detalles de la venta
             */
            foreach ($request->producto_id as $index => $productoId) {

                $cantidad = $request->cantidad[$index];
                $cambio = floatval($request->cambio);

                /**
                 * creamos el detalle de la venta
                 */
                SalesDetails::create([
                    'sale_id'        => $sale->id,
                    'producto_id'    => $productoId,
                    'cantidad'       => $request->cantidad[$index],
                    'precio_unitario' => $request->precio_unitario[$index],
                    'sub_total'      => $request->sub_total[$index],
                    'cambio'         =>  $cambio,
                    'descuento_porcentaje' => $request->descuento_porcentaje[$index] ?? null,
                    'descuento_en_dolar'  => $request->descuento_en_dolar[$index] ?? null,
                ]);

                /**
                 * 
                 * actualizamos el stock de los productos
                 */
                $producto = Producto::find($productoId);
                if ($producto->stock < $cantidad) {
                    throw new \Exception("El stock es insuficiente para el producto: {$producto->nombre}");
                }

                $producto->stock -= $cantidad;
                $producto->save();
            }

            DB::commit();

            $pdf = Pdf::loadView('postSales.ticket', [
                'venta' => $sale->load('clientes', 'detalles.producto'),
            ]);

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="venta_ticket.pdf"');


            return response()->json([
                'success' => 'Venta registrada correctamente.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 422);
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
