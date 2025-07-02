<?php

namespace App\Http\Controllers\Categorias;

use App\Http\Controllers\Controller;
use App\Models\Categoria\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Yajra\DataTables\Facades\DataTables;

class CategoriaController extends Controller
{
    public function index()
    {
        return view('categorias.index');
    }

    public function getCategories(Request $request)
    {
        if ($request->ajax()) {
            $data = Categoria::getCategories();
            return DataTables::of($data)
                ->addColumn('categoria_nombre', function ($data) {
                    return $data->categoria_nombre ?? 'sin datos';
                })
                ->addColumn('categoria_descripcion', function ($data) {
                    return $data->categoria_descripcion ?? 'sin datos';
                })
                ->addColumn('estado', function ($data) {
                    if ($data->estado == 'ACTIVE') {
                        return '<span class="badge bg-success">Activa</span>';
                    } else {
                        return '<span class="badge bg-danger">Deshabilitada</span>';
                    }
                })
                ->addColumn('acciones', function ($data) {
                    $editar = '';
                    $eliminar = '';
                    $eliminarUrl = "javascript:void(0)";
                    $onClickEliminar = "confirmDeleteCategoria({$data->id}); return false;";

                    if (Auth()->user()->can('categoria_edit')) {
                    $editar = '<a href="#" 
                                    class="btn bg-label-primary mt-mobile w-90 mx-2 btn-editar-categoria"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCategoria"
                                    data-id="' . $data->id . '"
                                    data-nombre="' . e($data->categoria_nombre) . '"
                                    data-descripcion="' . e($data->categoria_descripcion) . '"
                                    data-estado="' . $data->estado . '"
                                    title="Editar">
                                    <i class="bx bx-edit" style="font-size: 20px; transition: transform 0.2s;"></i>
                             </a>';
                     }

                    if (Auth()->user()->can('categoria_delete')) {
                    $eliminar = '<a title="Eliminar" class="btn bg-label-danger mt-mobile mx-2" href="' . $eliminarUrl . '" onclick="' . $onClickEliminar . '">
                                    <i class="bx bx-trash-alt" style="font-size: 20px; transition: transform 0.2s;"></i>
                                 </a>';
                    }

                    return $editar . $eliminar;
                })
                ->rawColumns(['estado', 'acciones'])->make(true);
        }
    }

    public function storeCategoria(Request $request)
    {
        $request->validate(
            [
                'categoria_nombre' => 'required|unique:categorias,categoria_nombre',
                'categoria_descripcion' => 'required',
                'estado' => 'required',
            ],
            [
                'categoria_nombre.required' => 'El nombre de la categoría es requerido.',
                'categoria_nombre.unique' => 'El nombre de la categoría ya existe.',
                'categoria_descripcion.required' => 'La descripción de la categoría es requerida.',
                'estado.required' => 'El estado de la categoría es requerido.'
            ]
        );

        $categoria = Categoria::create([
            'categoria_nombre' => $request->categoria_nombre,
            'categoria_descripcion' => $request->categoria_descripcion,
            'estado' => $request->estado,
        ]);

        $categoria->save();

        if ($categoria) {
            return response()->json(['success' => 'La categoria se creo exitosamente'], 200);
        }

        return response()->json(['error' => 'Algo salio mal al intentar guardar la categoria'], 422);
    }

    public function actualizarCategoria(Request $request, $id)
    {
        $request->validate(
            [
                'categoria_nombre' => 'required|unique:categorias,categoria_nombre',
                'categoria_descripcion' => 'required',
                'estado' => 'required',
            ],
            [
                'categoria_nombre.required' => 'El nombre de la categoría es requerido.',
                'categoria_nombre.unique' => 'El nombre de la categoría ya existe.',
                'categoria_descripcion.required' => 'La descripción de la categoría es requerida.',
                'estado.required' => 'El estado de la categoría es requerido.'
            ]
        );

        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['error' => 'No se encuentra la categoria'], 422);
        }

        $categoria->update([
            'categoria_nombre' => $request->categoria_nombre,
            'categoria_descripcion' => $request->categoria_descripcion,
            'estado' => $request->estado,
        ]);

        if ($categoria) {
            return response()->json(['success' => 'La categoria se actualizo con exito'], 200);
        }

        return response()->json(['error' => 'La categoria no pudo ser actualizada'], 422);
    }


    public function deleteCategoria($id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json(['error' => 'La categoria no fue encontrada por lo tanto no se puede eliminar'], 422);
        }

        if($categoria->productos()->count() > 0)
        {
            return response()->json(['error' => 'No se puede eliminar la categoría porque tiene productos asociados'], 405);
        }

        $categoria->delete();

        if ($categoria) {
            return response()->json(['success' => 'La categoria se elimino con exito'], 200);
        }

        return response()->json(['error' => 'La categoria no pudo ser eliminada'], 422);
    }
}
