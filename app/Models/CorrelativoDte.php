<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrelativoDte extends Model
{
    protected $table = 'correlativo_dtes';
    protected $fillable = [
        'tipo_dte',
        'codigo_establecimiento',
        'correlativo',
    ];
}
