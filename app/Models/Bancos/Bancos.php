<?php

namespace App\Models\Bancos;

use Illuminate\Database\Eloquent\Model;

class Bancos extends Model
{
    protected $table = 'bancos';

    protected $fillable = [
        'nombre',
        'codigo',
        'estado'
    ];

    public function cuentas()
    {
        return $this->hasMany(CuentasBancarias::class, 'banco_id');
    }

    public static function getIndexBancos()
    {
        $data = Bancos::select('bancos.*')->orderBy('id', 'desc')->get();
        return $data;
    }
}
