<?php

namespace App\Models\Ubicaciones;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';

    protected $fillable = [
        'nombre',
        'codigo'
    ];
}
