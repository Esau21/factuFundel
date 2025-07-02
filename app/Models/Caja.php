<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table  = 'cajas';

    protected $fillable = [
        'user_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'total_efectivo',
        'total_tarjeta',
        'total_otros',
        'total_declarado',
        'diferencia',
        'estado',
        'observaciones'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getindexData()
    {
        $data = Caja::select('.*')->orderBy('id', 'desc')->get();

        return $data;
    }
}
