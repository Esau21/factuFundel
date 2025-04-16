<?php

namespace App\Models\Categoria;

use App\Models\Producto\Producto;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    protected $table = 'categorias';

    protected $fillable = [
        'categoria_nombre',
        'categoria_descripcion',
        'estado'
    ];

    public static function getCategories()
    {
        $data = Categoria::select('*')->orderBy('id', 'desc')->get();
        return $data;
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
