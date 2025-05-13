<?php

namespace App\Models\DGII;

use App\Models\SociosNegocios\Empresa;
use Illuminate\Database\Eloquent\Model;

class CertificadosDigitales extends Model
{
    protected $table = 'certificados_digitales';

    protected $fillable = [
        'empresa_id',
        'certificado',
        'clave',
        'fecha_expiracion'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
