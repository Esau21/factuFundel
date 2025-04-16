<?php

namespace App\Http\Controllers\Producto;

use App\Exports\ArrayExport;
use App\Exports\ProductosPlantillaExport;
use App\Http\Controllers\Controller;
use App\Imports\ProductosImport;
use App\Models\Categoria\Categoria;
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
        $categorias = Categoria::all();
        return view('productos.index', compact('categorias'));
    }

    public function getIndexDataProductos(Request $request)
    {
        if ($request->ajax()) {
            $data = Producto::getProductosData();
            return DataTables::of($data)
                ->addColumn('imagen', function ($data) {
                    return '<img src="' . $data->imagen . '" alt="no image" class="img-fluid rounded" width="50px;" height="50px;">';
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
                    return '$' . number_format($data->precio_compra, 2) ?? '';
                })
                ->addColumn('precio_venta', function ($data) {
                    return '$' . number_format($data->precio_venta, 2) ?? '';
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
                    $editar = '';
                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeleteProducto({$data->id}); return false;";

                    $editar = '<a href="#" 
                                    class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-producto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editProducto"
                                    data-id="' . $data->id . '"
                                    data-codigo="' . e($data->codigo) . '"
                                    data-nombre="' . e($data->nombre) . '"
                                    data-categoria_id="' . e($data->categoria_id) . '"
                                    data-descripcion="' . e($data->descripcion) . '"
                                    data-precio_compra="' . e($data->precio_compra) . '"
                                    data-precio_venta="' . e($data->precio_venta) . '"
                                    data-stock="' . e($data->stock) . '"
                                    data-stock_minimo="' . e($data->stock_minimo) . '"
                                    data-unidad_medida="' . e($data->unidad_medida) . '"
                                    data-marca="' . e($data->marca) . '"
                                    data-estado="' . e($data->estado) . '"
                                    data-imagen="' . e($data->imagen) . '"
                                    title="Editar">
                                    <i class="bx bx-edit"></i>
                             </a>';


                    $eliminar = '<a title="Eliminar" class="btn btn-danger mt-mobile mx-2" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                                    <i class="bx bx-trash-alt"></i>
                                 </a>';


                    return $editar . $eliminar;
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

    public function updateProducto(Request $request, $id)
    {
        $request->validate([
            'codigo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|numeric',
            'stock_minimo' => 'nullable|numeric',
            'unidad_medida' => 'nullable|string',
            'marca' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'Este producto no existe'], 422);
        }

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            $path = 'productos';

            $storagePath = storage_path('app/public/' . $path);
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true); /* Crear la carpeta 'productos' si no existe */
            }

            /* Eliminar la imagen anterior si existe */
            if ($producto->imagen && file_exists(public_path('storage/' . $producto->imagen))) {
                unlink(public_path('storage/' . $producto->imagen)); /* Borra la imagen anterior */
            }

            /* Obtener el nombre original de la imagen */
            $imagen = $request->file('imagen');
            $imagenName = time() . '_' . $imagen->getClientOriginalName(); /* Nombre único para evitar conflictos */

            /* Mover la imagen al directorio 'productos' en public/storage */
            $imagen->move(public_path('storage/' . $path), $imagenName);

            /* Guarda la ruta relativa de la imagen en la base de datos */
            $producto->imagen = $path . '/' . $imagenName;
        }

        /* Actualiza el producto, excepto el campo 'imagen' */
        $producto->update($request->except('imagen'));

        return response()->json(['success' => 'El producto fue actualizado con éxito'], 200);
    }

    public function deleteProducto($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'El producto que quieres eliminar no fue encontrado'], 422);
        }

        $imagePath = public_path('storage/' . $producto->imagen);
        if($producto->imagen && file_exists($imagePath)){
            @unlink($imagePath);
        }

        if($producto->delete()){
            return response()->json(['success' => 'Se elimino correctemante el producto'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al intentar eliminar el producto'], 422);
    }
}
