<?php

namespace App\Http\Controllers\Producto;

use App\Exports\ArrayExport;
use App\Exports\ProductosPlantillaExport;
use App\Http\Controllers\Controller;
use App\Imports\ProductosImport;
use App\Models\Categoria\Categoria;
use App\Models\Item;
use App\Models\Producto\Producto;
use App\Models\UnidadMedida;
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
        $unidades = UnidadMedida::all();
        $items = Item::all();
        return view('productos.index', compact('categorias', 'unidades', 'items'));
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
                ->addColumn('item', function ($data) {
                    return $data?->items?->nombre ?? 'no data';
                })
                ->addColumn('uMedida', function ($data) {
                    return $data?->unidad?->nombre ?? 'no data';
                })
                ->addColumn('precio_compra', function ($data) {
                    return '$' . number_format($data->precio_compra, 2) ?? '';
                })
                ->addColumn('precio_venta', function ($data) {
                    return '$' . number_format($data->precio_venta, 2) ?? '';
                })
                ->addColumn('stock', function ($data) {
                    if ($data->stock >= 20) {
                        return '<span class="badge rounded-pill bg-primary text-white px-3 py-1">' . $data->stock . '</span>';
                    } else {
                        return '<span class="badge rounded-pill bg-warning text-white px-3 py-1">' . $data->stock . '</span>';
                    }
                })
                ->addColumn('stock_minimo', function ($data) {
                    if ($data->stock_minimo >= 20) {
                        return '<span class="badge rounded-pill bg-success text-white px-3 py-1">' . $data->stock_minimo . '</span>';
                    } else {
                        return '<span class="badge rounded-pill bg-warning text-white px-3 py-1">' . $data->stock_minimo . '</span>';
                    }
                })
                ->addColumn('estado', function ($data) {
                    if ($data->estado == 'activo') {
                        return '<span class="badge badge-center rounded-pill text-bg-success"><i class="icon-base bx bx-check"></i></span>';
                    } else {
                        return '<span class="badge badge-center rounded-pill text-bg-danger"><i class="icon-base bx bx-x-circle"></i></span>';
                    }
                })
                ->addColumn('categoria', function ($data) {
                    return $data->categoria ?? 'sin categoria';
                })
                ->addColumn('acciones', function ($data) {
                    $editar = '';
                    $eliminar = '';
                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeleteProducto({$data->id}); return false;";

                    if (Auth()->user()->can('edit_producto')) {
                        $editar = '<a href="#" 
                                    class="btn btn-primary mt-mobile w-90 mx-2 btn-editar-producto"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editProducto"
                                    data-id="' . $data->id . '"
                                    data-codigo="' . e($data->codigo) . '"
                                    data-nombre="' . e($data->nombre) . '"
                                    data-categoria_id="' . e($data->categoria_id) . '"
                                    data-item_id="' . e($data->item_id) . '"
                                    data-unidad_medida_id="' . e($data->unidad_medida_id) . '"
                                    data-descripcion="' . e($data->descripcion) . '"
                                    data-precio_compra="' . e($data->precio_compra) . '"
                                    data-precio_venta="' . e($data->precio_venta) . '"
                                    data-stock="' . e($data->stock) . '"
                                    data-stock_minimo="' . e($data->stock_minimo) . '"
                                    data-estado="' . e($data->estado) . '"
                                    data-imagen="' . e($data->imagen) . '"
                                    title="Editar">
                                    <i class="bx bx-edit"></i>
                             </a>';
                    }

                    if (Auth()->user()->can('delete_producto')) {
                        $eliminar = '<a title="Eliminar" class="btn btn-danger mt-mobile mx-2" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                                    <i class="bx bx-trash-alt"></i>
                                 </a>';
                    }
                    return $editar . $eliminar;
                })
                ->rawColumns(['acciones', 'estado', 'imagen', 'stock', 'stock_minimo'])->make(true);
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
        if (empty($import->validos) && $productosExistentes->isNotEmpty()) {
            $nombreArchivoError = 'errores_productos_' . now()->format('Ymd_His') . '.xlsx';
            $rutaError = 'exports/' . $nombreArchivoError;
            Excel::store(new ArrayExport($productosExistentes->values()->toArray()), $rutaError, 'public');

            $errores_url = asset('storage/' . $rutaError);
            return response()->json([
                'success' => false,
                'message' => 'Todos los productos ya existen. No se insertó ningún producto.',
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

    public function addProduct()
    {
        $categorias = Categoria::all();
        $unidades = UnidadMedida::all();
        $items = Item::all();

        return view('productos.add', compact('categorias', 'unidades', 'items'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'item_id' => 'required',
            'unidad_medida_id' => 'required',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|numeric',
            'stock_minimo' => 'nullable|numeric',
            'estado' => 'required|in:activo,deshabilitado',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no debe superar los 255 caracteres.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no debe superar los 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.numeric' => 'El precio de compra debe ser un número.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un número.',
            'stock_minimo.numeric' => 'El stock mínimo debe ser un número.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser "activo" o "deshabilitado".',
            'categoria_id.required' => 'Debes seleccionar una categoría válida.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ]);

        $producto = new Producto($request->except('imagen'));

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            $path = 'productos';

            $storagePath = storage_path('app/public/' . $path);
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $imagen = $request->file('imagen');
            $imagenName = time() . '_' . $imagen->getClientOriginalName();

            $imagen->move(public_path('storage/' . $path), $imagenName);

            $producto->imagen = $path . '/' . $imagenName;
        }

        if ($producto) {
            $producto->save();
            return response()->json(['success' => 'Producto agregado corretamente'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al agregar el producto'], 422);
    }

    public function updateProducto(Request $request, $id)
    {
        $request->validate([
            'codigo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'item_id' => 'required',
            'unidad_medida_id' => 'required',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|numeric',
            'stock_minimo' => 'nullable|numeric',
            'estado' => 'required|in:activo,deshabilitado',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.string' => 'El código debe ser una cadena de texto.',
            'codigo.max' => 'El código no debe superar los 255 caracteres.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.string' => 'El nombre debe ser una cadena de texto.',
            'nombre.max' => 'El nombre no debe superar los 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.numeric' => 'El precio de compra debe ser un número.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser un número.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.numeric' => 'El stock debe ser un número.',
            'stock_minimo.numeric' => 'El stock mínimo debe ser un número.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser "activo" o "deshabilitado".',
            'categoria_id.required' => 'Debes seleccionar una categoría válida.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
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
        if ($producto->imagen && file_exists($imagePath)) {
            @unlink($imagePath);
        }

        if ($producto->delete()) {
            return response()->json(['success' => 'Se elimino correctemante el producto'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al intentar elimnar el producto'], 422);
    }
}
