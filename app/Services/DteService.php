<?php

namespace App\Services;

use App\Models\SociosNegocios\Empresa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DteService
{
    private const URL_LOGIN = 'https://apitest.dtes.mh.gob.sv/seguridad/auth';
    private const URL_FIRMADOR = 'http://5.34.178.82:8113/firmardocumento/';
    private const URL_SEND = 'https://apitest.dtes.mh.gob.sv/fesv/recepciondte';
    private const URL_SEND_ANULACION = 'https://apitest.dtes.mh.gob.sv/fesv/anulardte';
    private const URL_SEND_CONTINGENCIA = 'https://apitest.dtes.mh.gob.sv/fesv/contingencia';

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

    public static function enviarDTE($dteFirmado, $empresa, $tipoDte, $codigoGeneracion)
    {
        switch ($tipoDte) {
            case '03':
            case '05':
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
            "ambiente" => $empresa->ambiente ?? '00',
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


    public function anularDTE(Request $request, $documento)
    {
        $empresa = $documento->empresa;
        $token = $this->loginMH($empresa);

        $cliente = $documento->cliente;
        if (!$cliente) {
            throw new \Exception("No se encontró cliente relacionado al documento.");
        }

        $tiposValidos = [1, 2, 3];
        $tipoAnulacion = (int)$request->tipo_invalidacion;

        if (!in_array($tipoAnulacion, $tiposValidos)) {
            throw new \Exception("Tipo de anulación inválido: $tipoAnulacion");
        }

        // Validar y formatear codigoGeneracion UUID
        $codigoGeneracion = strtoupper($documento->codigo_generacion);
        if (!preg_match('/^[A-F0-9]{8}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{4}-[A-F0-9]{12}$/', $codigoGeneracion)) {
            throw new \Exception("El código de generación no tiene el formato UUID válido.");
        }

        // Validar selloRecibido 40 caracteres
        $selloRecibido = trim(strtoupper($documento->sello_recibido));
        if (strlen($selloRecibido) !== 40) {
            throw new \Exception("El sello recibido debe tener exactamente 40 caracteres.");
        }

        $montoIva = floatval($documento->monto_iva);
        if ($montoIva < 0) {
            $montoIva = 0.00;
        }

        $tipoDte = str_pad($documento->tipo_documento, 2, '0', STR_PAD_LEFT);

        $documentoArray = [
            "tipoDte" => $tipoDte,
            "codigoGeneracion" => $codigoGeneracion,
            "selloRecibido" => $selloRecibido,
            "numeroControl" => $documento->numero_control,
            "fecEmi" => Carbon::parse($documento->fecha_emision)->format('Y-m-d'),
            "montoIva" => $montoIva,
            "nombre" => $cliente->nombre ?? null,
            "telefono" => $cliente->telefono ?? null,
            "correo" => $cliente->correo_electronico ?? null,
        ];

        // Para CCF (03), el numDocumento debe ser el NIT y tipoDocumento '13'
        if ($tipoDte === '03') {
            $documentoArray["tipoDocumento"] = $cliente->tipo_documento ?? null; // Código para NIT
            $documentoArray["numDocumento"] = $cliente->nit ?? null;
        } else {
            // Para otros documentos
            $documentoArray["tipoDocumento"] = $cliente->tipo_documento ?? null;
            $documentoArray["numDocumento"] = $cliente->numero_documento ?? null;
        }

        if ($tipoAnulacion === 2) {
            $documentoArray["codigoGeneracionR"] = $request->codigo_generacion_reemplazo;
        }

        $json = [
            "identificacion" => [
                "version" => 2,
                "ambiente" => $empresa->ambiente,
                "codigoGeneracion" => $codigoGeneracion,
                "fecAnula" => now()->format('Y-m-d'),
                "horAnula" => now()->format('H:i:s'),
            ],
            "emisor" => [
                "nit" => $empresa->nit,
                "nombre" => $empresa->nombre,
                "tipoEstablecimiento" => (string)$empresa->tipoEstablecimiento,
                "nomEstablecimiento" => $empresa->nombre_establecimiento,
                "codEstableMH" => $empresa->cod_estable_mh,
                "codEstable" => $empresa->cod_establecimiento,
                "codPuntoVentaMH" => $empresa->cod_punto_venta_mh,
                "codPuntoVenta" => $empresa->cod_punto_venta,
                "telefono" => $empresa->telefono,
                "correo" => $empresa->correo,
            ],
            "documento" => $documentoArray,
            "motivo" => [
                "tipoAnulacion" => $tipoAnulacion,
                "motivoAnulacion" => $request->motivo_anulacion,
                "nombreResponsable" => $request->nombre_responsable,
                "tipDocResponsable" => $request->tipo_doc_responsable,
                "numDocResponsable" => $request->num_doc_responsable,
                "nombreSolicita" => $request->nombre_solicita,
                "tipDocSolicita" => $request->tipo_doc_solicita,
                "numDocSolicita" => $request->num_doc_solicita,
            ],
        ];

        $jsonFirmado = self::firmarDTE($json, $empresa);

        $payload = [
            "ambiente" => $empresa->ambiente,
            "idEnvio" => 1,
            "version" => 2,
            "documento" => $jsonFirmado,
        ];

        $response = Http::withToken($token)
            ->post(self::URL_SEND_ANULACION, $payload);

        $documento->mh_response = $response->body();
        $documento->save();

        return $response->json();
    }

    public static function enviarEventoContingencia(Request $request, Empresa $empresa, $documento)
    {
        $token = self::loginMH($empresa);

        $codigoGeneracion = strtoupper($documento->codigo_generacion);
        if (!Str::isUuid($codigoGeneracion)) {
            throw new \Exception("El código de generación no es UUID válido.");
        }

        $tipoDte = $documento->tipo_documento;
        if (!preg_match('/^0[1-9]$|^1[0-5]$/', $tipoDte)) {
            throw new \Exception("tipoDoc no cumple con el formato requerido: $tipoDte");
        }
        $version = 3;
        $ambiente = in_array($empresa->ambiente, ['00', '01']) ? $empresa->ambiente : '00';

        // Función para agregar segundos si faltan
        $fixHora = function ($hora) {
            if (preg_match('/^\d{2}:\d{2}$/', $hora)) {
                return $hora . ':00';
            }
            return $hora;
        };

        $fInicio = \Carbon\Carbon::parse($request->fInicio)->format('Y-m-d');
        $fFin = \Carbon\Carbon::parse($request->fFin)->format('Y-m-d');
        $hInicio = $fixHora($request->hInicio);
        $hFin = $fixHora($request->hFin);

        $motivoContingencia = $request->motivoContingencia ?? '';
        if ((int)$request->tipoContingencia === 5 && trim($motivoContingencia) === '') {
            throw new \Exception("El motivoContingencia es obligatorio cuando tipoContingencia es 5");
        }

        $evento = [
            "identificacion" => [
                "version" => $version,
                "ambiente" => $ambiente,
                "codigoGeneracion" => $codigoGeneracion,
                "fTransmision" => now()->format('Y-m-d'),
                "hTransmision" => now()->format('H:i:s'),
            ],
            "emisor" => [
                "nit" => $empresa->nit,
                "nombre" => $empresa->nombre,
                "nombreResponsable" => $request->nombre_responsable,
                "tipoDocResponsable" => $request->tipo_doc_responsable,
                "numeroDocResponsable" => $request->num_doc_responsable,
                "tipoEstablecimiento" => (string)$empresa->tipoEstablecimiento,
                "codEstableMH" => $empresa->cod_estable_mh ?? null,
                "codPuntoVenta" => $empresa->cod_punto_venta_mh ?? null,
                "telefono" => $empresa->telefono,
                "correo" => $empresa->correo,
            ],
            "detalleDTE" => [
                [
                    "noItem" => 1,
                    "codigoGeneracion" => $codigoGeneracion,
                    "tipoDoc" => $tipoDte,
                ]
            ],
            "motivo" => [
                "fInicio" => $fInicio,
                "fFin" => $fFin,
                "hInicio" => $hInicio,
                "hFin" => $hFin,
                "tipoContingencia" => (int)$request->tipoContingencia,
                "motivoContingencia" => $motivoContingencia,
            ]
        ];

        $eventoFirmado = self::firmarDTE($evento, $empresa);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'User-Agent' => 'LaravelApp',
            'Content-Type' => 'application/json',
        ])->post(self::URL_SEND_CONTINGENCIA, [
            'nit' => $empresa->nit,
            'documento' => $eventoFirmado,
        ]);

        if (!$response->ok()) {
            throw new \Exception("Error al enviar evento de contingencia: " . $response->body());
        }

        return $response->json();
    }
}
