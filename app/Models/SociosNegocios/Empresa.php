<?php

namespace App\Models\SociosNegocios;

use App\Models\ActividadEconomica;
use App\Models\Ubicaciones\Departamento;
use App\Models\Ubicaciones\Municipio;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'nombreComercial',
        'departamento_id',
        'municipio_id',
        'complemento',
        'actividad_economica_id',
        'nrc',
        'nit',
        'giro',
        'telefono',
        'correo',
        'logo',
    ];

    public static function getDataEmpresa()
    {
        $data = Empresa::select('*')->orderBy('id', 'desc')->get();
        return $data;
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function actividad()
    {
        return $this->belongsTo(ActividadEconomica::class, 'actividad_economica_id');
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
