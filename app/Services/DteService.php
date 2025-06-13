<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class DteService
{
    private const URL_LOGIN = 'https://apitest.dtes.mh.gob.sv/seguridad/auth';
    private const URL_FIRMADOR = 'http://5.34.178.82:8113/firmardocumento/';
    private const URL_SEND = 'https://apitest.dtes.mh.gob.sv/fesv/recepciondte';

    public static function loginMH($empresa)
    {
        $usuario = $empresa->nit;
        $password = Crypt::decryptString($empresa->claveAPI);
        $response = Http::asMultipart()->post(self::URL_LOGIN, [
            [
                'name' => 'user',
                'contents' => $usuario,
            ],
            [
                'name' => 'pwd',
                'contents' => $password,
            ],
        ]);

        if (!$response->ok()) {
            throw new \Exception("Error al hacer login con MH: " . $response->body());
        }

        $data = $response->json();

        if (!isset($data['body']['token'])) {
            throw new \Exception("Login fallido. Respuesta MH: " . json_encode($data));
        }

        $tokenLimpio = str_replace('Bearer ', '', $data['body']['token']);

        $empresa->update([
            'token' => $tokenLimpio,
            'token_expire' => Carbon::now()->addHour(),
        ]);

        return $tokenLimpio;
    }

    public static function firmarDTE($jsonDte, $empresa)
    {
        $certPassword = Crypt::decryptString($empresa->claveCert);

        if (is_string($jsonDte)) {
            $jsonDte = json_decode($jsonDte, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON inválido en firmarDTE");
            }
        }

        $payloadArray = [
            "nit" => $empresa->nit,
            "activo" => true,
            "passwordPri" => $certPassword,
            "dteJson" => $jsonDte,
        ];

        $payloadJson = json_encode($payloadArray, JSON_UNESCAPED_UNICODE);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withBody($payloadJson, 'application/json')
            ->post(self::URL_FIRMADOR);

        if (!$response->ok()) {
            throw new \Exception("Error al firmar DTE: " . $response->body());
        }

        $data = $response->json();

        if (!isset($data['body']) || $data['status'] !== 'OK') {
            throw new \Exception("Respuesta inválida del firmador. No se encontró 'body': " . json_encode($data));
        }

        return $data['body'];
    }

    public static function enviarDTE($dteFirmado, $empresa, $tipoDte, $codigoGeneracion, $ambiente = "00")
    {
        switch ($tipoDte) {
            case '03':
                $version = 3;
                break;
            case '01':
            case '14':
            case '15':
            default:
                $version = 1;
                break;
        }

        $payload = [
            "ambiente" => $ambiente,
            "idEnvio" => 1,
            "version" => $version,
            "tipoDte" => $tipoDte,
            "documento" => $dteFirmado,
            "codigoGeneracion" => $codigoGeneracion,
        ];

        $response = Http::withToken($empresa->token)->post(self::URL_SEND, $payload);

        if (!$response->ok()) {
            throw new \Exception("Error al enviar DTE: " . $response->body());
        }

        return $response->json();
    }
}
