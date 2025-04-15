<?php

namespace App\Models\Proveedor;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedors';


    protected $fillable = [
        'nombre',
        'nrc',
        'nit',
        'telefono',
        'correo',
        'direccion',
        'giro',
        'contacto_nombre',
        'contacto_cargo',
        'notas',
        'estado'
    ];


    public static function getIndexData()
    {
        $data = Proveedor::all();
        return $data;
    }
}
