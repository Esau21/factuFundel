<?php

namespace App\Models\Ubicaciones;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = [
        'codigo',
        'nombre'
    ];
}
