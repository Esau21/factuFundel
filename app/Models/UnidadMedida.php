<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidad_medidas';

    protected $fillable = [
        'codigo',
        'nombre'
    ];
}
