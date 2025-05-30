<?php

namespace App\Models\SociosNegocios;

use App\Models\ActividadEconomica;
use App\Models\Ubicaciones\Departamento;
use App\Models\Ubicaciones\Municipio;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'nombreComercial',
        'tipo_documento',
        'numero_documento',
        'nit',
        'nrc',
        'direccion',
        'departamento_id',
        'municipio_id',
        'telefono',
        'correo_electronico',
        'tipo_contribuyente',
        'actividad_economica_id',
        'tipo_persona',
        'es_extranjero',
        'pais',
        'empresa_id'
    ];


    public function empresas()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function actividad()
    {
        return $this->belongsTo(ActividadEconomica::class, 'actividad_economica_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public static function getDtaClientes($tipo = null)
    {
        $query = Clientes::select('clientes.*')
            ->with('departamento', 'municipio', 'actividad')
            ->orderBy('clientes.id', 'desc');

        if ($tipo) {
            $query->where('tipo_persona', $tipo);
        }

        $data = $query->get();
        return $data;
    }
}
