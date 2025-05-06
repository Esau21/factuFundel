<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('empresalogo')) {
    function empresaLogo()
    {
        $user = Auth::user();


        if ($user && $user->empresa && $user->empresa->logo) {
            return asset('storage/' . $user->empresa->logo);
        }

        return asset('img/camara1.png');
    }
}
