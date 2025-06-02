<?php

namespace App\Helpers;

class NumeroALetras
{
    public static function convertir($numero, $moneda = 'DÓLARES')
    {
        $formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
        $entero = floor($numero);
        $decimales = round(($numero - $entero) * 100);

        $letras = strtoupper($formatter->format($entero));

        if ($decimales > 0) {
            $letras .= " CON " . str_pad($decimales, 2, "0", STR_PAD_LEFT) . "/100";
        } else {
            $letras .= " CON 00/100";
        }

        return $letras . " " . strtoupper($moneda);
    }
}
