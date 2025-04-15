<?php

namespace App\Http\Controllers\Producto;

use App\Exports\ArrayExport;
use App\Exports\ProductosPlantillaExport;
use App\Http\Controllers\Controller;
use App\Imports\ProductosImport;
use App\Models\Producto\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ProductoController extends Controller
{
    public function index()
    {
        return view('productos.index');
    }

    public function getIndexDataProductos(Request $request)
    {
        if ($request->ajax()) {
            $data = Producto::getProductosData();
            return DataTables::of($data)
                ->addColumn('imagen', function ($data) {
                    return ' <img src="' . $data->imagen . '" alt="no image" class="img-fluid rounded" width="50px;" height="50px;">';
                })
                ->addColumn('codigo', function ($data) {
                    return $data->codigo ?? 'no data';
                })
                ->addColumn('nombre', function ($data) {
                    return $data->nombre ?? 'no data';
                })
                ->addColumn('descripcion', function ($data) {
                    return $data->descripcion ?? 'no data';
                })
                ->addColumn('precio_compra', function ($data) {
                    return number_format($data->precio_compra, 2) ?? '';
                })
                ->addColumn('precio_venta', function ($data) {
                    return number_format($data->precio_venta, 2) ?? '';
                })
                ->addColumn('stock', function ($data) {
                    return $data->stock ?? 'no data';
                })
                ->addColumn('stock_minimo', function ($data) {
                    return $data->stock_minimo ?? 'no data';
                })
                ->addColumn('unidad_medida', function ($data) {
                    return $data->unidad_medida ?? 'no data';
                })
                ->addColumn('marca', function ($data) {
                    return $data->marca ?? 'no data';
                })
                ->addColumn('estado', function ($data) {
                    return $data->estado ?? 'no data';
                })
                ->addColumn('categoria', function ($data) {
                    return $data->categoria ?? 'sin categoria';
                })
                ->addColumn('acciones', function ($data) {
                    return 'acciones';
                })
                ->rawColumns(['acciones', 'estado', 'imagen'])->make(true);
        }
    }

    public function descargarPlantilla()
    {
        return Excel::download(new ProductosPlantillaExport, 'plantilla_productos.xlsx');
    }

    public function enviarCargaMasivadeProductos(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        /* Ruta pública donde se guardarán los archivos */
        $publicPath = public_path('uploads/productos');
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        /* Guardar archivo en el servidor */
        $file = $request->file('file');
        $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($publicPath, $fileName);

        /* Ruta completa del archivo para usar en la respuesta */
        $fileUrl = asset('uploads/productos/' . $fileName);

        /* Ejecutamos la importación */
        $import = new ProductosImport();
        Excel::import($import, $publicPath . '/' . $fileName);

        /* Verificar si hay productos duplicados (con error 'Producto con código o nombre ya existe') */
        $productosExistentes = collect($import->errores)->filter(function ($producto) {
            return isset($producto['error']) && $producto['error'] === 'Producto con código o nombre ya existe';
        });

        /* Si hay productos duplicados, solo devolvemos el archivo de errores */
        if ($productosExistentes->isNotEmpty()) {
            /**
             * Exportar errores (productos existentes)
             */
            $nombreArchivoError = 'errores_productos_' . now()->format('Ymd_His') . '.xlsx';
            $rutaError = 'exports/' . $nombreArchivoError;
            Excel::store(new ArrayExport($productosExistentes->values()->toArray()), $rutaError, 'public');
            $errores_url = asset('storage/' . $rutaError);

            return response()->json([
                'success' => false,
                'message' => 'Algunos productos ya existen. Solo se descargará el archivo de errores.',
                'errores_url' => $errores_url,
                'file_url' => $fileUrl,
            ]);
        }

        /**
         *  Si no hay errores, exportar productos válidos
         */
        $productos_url = null;
        if (!empty($import->validos)) {
            $nombreArchivoExito = 'productos_subidos_correctamente_' . now()->format('Ymd_His') . '.xlsx';
            $rutaExito = 'exports/' . $nombreArchivoExito;
            Excel::store(new ArrayExport($import->validos), $rutaExito, 'public');
            $productos_url = asset('storage/' . $rutaExito);
        }


        return response()->json([
            'success' => true,
            'productos' => $import->validos,
            'productos_url' => $productos_url,
            'file_url' => $fileUrl,
        ]);
    }
}
