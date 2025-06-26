<?php

namespace App\Models\DGII;

use App\Models\SociosNegocios\Clientes;
use App\Models\SociosNegocios\Empresa;
use Carbon\Carbon;
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
        'sello_recibido',
        'tipo_transmision',
        'mh_response'
    ];

    public function documentoReferenciado()
    {
        return $this->hasOne(DocumentosDte::class, 'numero_control', 'referencia_numero_control');
    }

    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public static function getData($tipo = '', $clienteId = null, $fechaInicio = null, $fechaFin = null)
    {
        $query = DocumentosDte::with(['cliente', 'empresa'])->orderBy('id', 'desc');

        if (in_array($tipo, ['01', '03', '14', '15', '05'])) {
            $query->where('tipo_documento', $tipo);
        }

        if ($clienteId) {
            $query->where('cliente_id', $clienteId);
        }

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_emision', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);
        } elseif ($fechaInicio) {
            $query->where('fecha_emision', '>=', Carbon::parse($fechaInicio)->startOfDay());
        } elseif ($fechaFin) {
            $query->where('fecha_emision', '<=', Carbon::parse($fechaFin)->endOfDay());
        }

        return $query->get();
    }
}
