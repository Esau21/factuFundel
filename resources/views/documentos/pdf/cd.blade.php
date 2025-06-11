<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>COMPROBANTE DE DONACIÓN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="{{ public_path('css/custom_pdf.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_path('css/custom_page.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <section class="header" style="top:-250px;">
        <table class="table m-0" style="width: 100%;">
            <tr>
                <td colspan="2" class="p-2" style="width: 45%;">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Logo a la izquierda -->
                        <div style="max-width: 70px;">
                            <img src="{{ empresaLogo() }}" alt="Logo" class="img-fluid"
                                style="max-height: 70px; display: block;">
                        </div>
                        <!-- Info del emisor a la derecha, en la misma línea -->
                        <div style="font-size: 10px; line-height: 1.3;">
                            <div><strong>NOMBRE:</strong> {{ $json['donatario']['nombre'] }}</div>
                            <div><strong>GIRO:</strong> {{ $json['donatario']['descActividad'] }}</div>
                            <div>
                                <strong>N°DOCUMENTO:</strong> {{ $json['donatario']['numDocumento'] }}
                                &nbsp;&nbsp;&nbsp;
                                <strong>NRC:</strong> {{ $json['donatario']['nrc'] }} &nbsp;&nbsp;&nbsp;
                                <strong>TEL:</strong> {{ $json['donatario']['telefono'] }}
                            </div>
                            <div><strong>DIRECCIÓN:</strong> {{ $json['donatario']['direccion']['complemento'] }}</div>
                            <div><strong>Email:</strong> {{ $json['donatario']['correo'] }}</div>
                            <div><strong>Web:</strong> www.fundelong.com</div>
                        </div>
                    </div>
                </td>
                <td class="text-center align-middle p-2" style="width: 15%;">
                    @php
                        $qrUrl =
                            'https://admin.factura.gob.sv/consultaPublica?ambiente=' .
                            $json['identificacion']['ambiente'] .
                            '&codGen=' .
                            $json['identificacion']['codigoGeneracion'] .
                            '&fechaEmi=' .
                            $json['identificacion']['fecEmi'];
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrUrl) }}" width="80"
                        alt="QR" class="img-fluid">
                </td>
                <td class="align-middle p-3" style="width: 40%;">
                    <div class="border border-dark p-3 rounded"
                        style="font-size: 10px; line-height: 1.3; background: #fff;">
                        <div class="text-center text-uppercase text-dark fw-bold">Documento Tributario Electrónico</div>
                        <div class="fw-bold text-center mb-3">
                            {{ $json['identificacion']['tipoDte'] === '15' ? 'COMPROBANTE DE DONACIÓN' : '' }}
                        </div>
                        <strong>Código de generación:</strong><br>
                        {{ $json['identificacion']['codigoGeneracion'] }}<br>
                        <strong>Número de Control:</strong><br>
                        {{ $json['identificacion']['numeroControl'] }}<br>
                        <strong>Sello de Recepción:</strong><br>
                        {{ $mh['selloRecibido'] ?? '—' }}
                    </div>
                </td>
            </tr>
        </table>
    </section>

    <div class="changes" style="margin-top:-60px;">
        <div class="border border-dark rounded" style="background: #fff; font-size: 10px; line-height: 1;">
            <table class="table table-bordered w-100 mb-0">
                <tr>
                    <!-- Columna izquierda: CLIENTE -->
                    <td style="width: 50%; vertical-align: top; text-align: left; padding: 0.75rem;">
                        <p class="mb-1"><strong>DONANTE:</strong> {{ $json['donante']['nombre'] }}</p>
                        <p class="mb-1"><strong>N°DOCUMENTO:</strong> {{ $json['donante']['numDocumento'] }}</p>
                        <p class="mb-1"><strong>GIRO:</strong> {{ $json['donante']['descActividad'] }}</p>
                        <p class="mb-1"><strong>DIRECCIÓN:</strong>
                            {{ $json['donante']['direccion']['complemento'] }}</p>
                        <p class="mb-0"><strong>CORREO:</strong> {{ $json['donante']['correo'] }}</p>
                    </td>

                    <!-- Columna derecha: DETALLES -->
                    <td style="width: 50%; vertical-align: top; text-align: right; padding: 0.75rem;">
                        <p class="mb-1"><strong>FECHA:</strong> {{ $json['identificacion']['fecEmi'] }}
                            {{ $json['identificacion']['horEmi'] }}</p>
                        <p class="mb-0"><strong>VENDEDOR:</strong> {{ Auth::user()->name }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <section style="margin-top: 60px;">
        <div class="table-responsive rounded overflow-hidden border border-dark">
            <table class="table table-bordered table-striped mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="p-1">Cantidad</th>
                        <th class="p-1">Código</th>
                        <th class="p-1">Descripción</th>
                        <th class="p-1">Valor Unitario</th>
                        <th class="p-1">Valor Total</th>
                        <th class="p-1">Depreciación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($json['cuerpoDocumento'] as $item)
                        <tr>
                            <td class="p-2">{{ $item['cantidad'] }}</td>
                            <td class="p-2">{{ $item['codigo'] }}</td>
                            <td class="p-2">{{ $item['descripcion'] }}</td>
                            <td class="p-2">${{ number_format($item['valorUni'], 2) }}</td>
                            <td class="p-2">${{ number_format($item['valor'], 2) }}</td>
                            <td class="p-2">${{ number_format($item['depreciacion'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>




    <div class="footer" style="border: none !important; font-size: 10px; line-height: 1.1;">
        <table class="table table-bordered mb-0"
            style="border-radius: 9px; border: 2px solid black; border-collapse: separate; border-spacing: 0;">
            <tbody>
                <tr>
                    <!-- Columna izquierda: Total en letras y línea negra -->
                    <td
                        style="width: 75%; vertical-align: top; padding: 0.3rem 0.5rem; border-right: 1px solid black; text-align: left;">
                        <strong>Son:</strong><br>
                        {{ $json['resumen']['totalLetras'] }}
                    </td>

                    <!-- Columna derecha: Total Donado + Detalle de pagos -->
                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <table class="table table-sm table-borderless mb-0"
                            style="font-size: 10px; border-collapse: collapse;">
                            <tbody>
                                <tr>
                                    <td style="border-right: 1px solid black; padding: 0.1rem 0.2rem;">Total Donado</td>
                                    <td class="text-end" style="padding: 0.1rem 0.2rem;">
                                        ${{ number_format($json['resumen']['valorTotal'], 2) }}
                                    </td>
                                </tr>
                                @foreach ($json['resumen']['pagos'] as $pago)
                                    <tr>
                                        <td style="border-right: 1px solid black; padding: 0.1rem 0.2rem;">
                                            Pago {{ $pago['codigo'] }}
                                        </td>
                                        <td class="text-end" style="padding: 0.1rem 0.2rem;">
                                            ${{ number_format($pago['montoPago'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        @if (!empty($json['otrosDocumentos']))
            <div class="mt-1"
                style="font-size: 9px; border: 1px solid black; border-radius: 6px; padding: 0.4rem; margin-top: 0.5rem;">
                <strong>Otros Documentos Asociados:</strong><br>
                @foreach ($json['otrosDocumentos'] as $documento)
                    <div style="margin-top: 0.3rem;">
                        <strong>Código:</strong>
                        {{ $documento['codDocAsociado'] == 1 ? 'Domiciliado en El Salvador' : ($documento['codDocAsociado'] == 2 ? 'No domiciliado en El Salvador' : 'N/A') }}<br>

                        <strong>Descripción:</strong> {{ $documento['descDocumento'] ?? 'N/A' }}<br>
                        <strong>Detalle:</strong> {{ $documento['detalleDocumento'] ?? 'N/A' }}
                    </div>
                @endforeach
            </div>
        @endif


        <div class="text-end mt-1" style="font-size: 8px; line-height: 1;">
            DTE emitido con software MoranZSoft One <br>
            Impreso por MoranZSoft One
        </div>
    </div>

</body>

</html>
