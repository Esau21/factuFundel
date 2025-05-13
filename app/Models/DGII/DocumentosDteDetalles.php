<?php

namespace App\Models\DGII;

use Illuminate\Database\Eloquent\Model;

class DocumentosDteDetalles extends Model
{
    protected $table = 'documentos_dte_detalles';

    protected $fillable = [
        'documento_dte_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'sub_total',
        'descuento',
        'iva',
    ];

    public function documentosdte()
    {
        return $this->belongsTo(DocumentosDte::class, 'documento_dte_id');
    }
}
