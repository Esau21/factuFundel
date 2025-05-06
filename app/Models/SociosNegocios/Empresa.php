<?php

namespace App\Models\SociosNegocios;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'nrc',
        'nit',
        'giro',
        'telefono',
        'correo',
        'direccion',
        'logo',
    ];

    public static function getDataEmpresa()
    {
        $data = Empresa::select('*')->orderBy('id', 'desc')->get();
        return $data;
    }

    public function getImagenAttribute($value)
    {
        $imagen = $value ?? $this->attributes['logo'] ?? null;

        if ($imagen && file_exists(public_path('storage/' . $imagen))) {
            return asset('storage/' . $imagen);
        } else {
            return asset('img/camara1.png');
        }
    }
}
