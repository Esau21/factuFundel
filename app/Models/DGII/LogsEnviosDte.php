<?php

namespace App\Models\DGII;

use Illuminate\Database\Eloquent\Model;

class LogsEnviosDte extends Model
{
    protected $table = 'logs_envios_dtes';

    protected $fillable = [
        'documento_dte_id',
        'fecha_envio',
        'estado_envio',
        'mensaje_dgii'
    ];

    public function documentoste()
    {
        return $this->belongsTo(DocumentosDte::class, 'documento_dte_id');
    }
}
