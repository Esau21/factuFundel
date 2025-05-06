<?php

namespace App\Models\SociosNegocios;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'tipo_documento',
        'numero_documento',
        'nit',
        'nrc',
        'giro',
        'direccion',
        'departamento',
        'municipio',
        'telefono',
        'correo_electronico',
        'tipo_contribuyente',
        'codigo_actividad',
        'tipo_persona',
        'es_extranjero',
        'pais',
        'empresa_id'
    ];


    public function empresas()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public static function getDtaClientes($tipo = null)
    {
        $query = Clientes::select('clientes.*')->orderBy('clientes.id', 'desc');

        if ($tipo) {
            $query->where('tipo_persona', $tipo);
        }

        $data = $query->get();
        return $data;
    }
}
