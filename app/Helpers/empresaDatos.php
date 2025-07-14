<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('empresaDatos')) {
    function empresaDatos()
    {
        $user = Auth::user();


        if ($user && $user->empresa && $user->empresa->nombre) {
            return $user->empresa->nombre;
        }

        return 'Nombre de tu empresa';
    }
}
