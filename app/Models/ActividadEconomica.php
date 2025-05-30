<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadEconomica extends Model
{
    protected $table = 'actividades_economicas';

    protected $fillable = [
        'codActividad',
        'descActividad'
    ];
}
