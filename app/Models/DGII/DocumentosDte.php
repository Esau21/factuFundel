<?php

namespace App\Models\DGII;

use App\Models\SociosNegocios\Clientes;
use App\Models\SociosNegocios\Empresa;
use Illuminate\Database\Eloquent\Model;

class DocumentosDte extends Model
{
    protected $table = 'documentos_dtes';

    protected $fillable = [
        'tipo_documento',
        'numero_control',
        'codigo_generacion',
        'fecha_emision',
        'cliente_id',
        'empresa_id',
        'estado',
        'json_dte',
    ];

    public function documentoReferenciado()
    {
        return $this->hasOne(DocumentosDte::class, 'numero_control', 'referencia_numero_control');
    }


    public function detallesdte()
    {
        return $this->hasMany(DocumentosDteDetalles::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
