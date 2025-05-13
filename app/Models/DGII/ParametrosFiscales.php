<?php

namespace App\Models\DGII;

use App\Models\SociosNegocios\Empresa;
use Illuminate\Database\Eloquent\Model;

class ParametrosFiscales extends Model
{
    protected $table = 'parametros_fiscales';

    protected $fillable = [
        'empresa_id',
        'nit',
        'nrc',
        'codigo_establecimiento',
        'codigo_punto_venta',
        'codigo_sucursal',
        'tipo_entorno',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
