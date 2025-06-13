<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>FACTURA SUJETO EXCLUIDO</title>
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
                            <div><strong>NOMBRE:</strong> {{ $json['emisor']['nombre'] }}</div>
                            <div><strong>GIRO:</strong> {{ $json['emisor']['descActividad'] }}</div>
                            <div>
                                <strong>NRC:</strong> {{ $json['emisor']['nrc'] }} &nbsp;&nbsp;&nbsp;
                                <strong>NIT:</strong> {{ $json['emisor']['nit'] }} &nbsp;&nbsp;&nbsp;
                                <strong>TEL:</strong> {{ $json['emisor']['telefono'] }}
                            </div>
                            <div><strong>DIRECCIÓN:</strong> {{ $json['emisor']['direccion']['complemento'] }}</div>
                            <div><strong>Email:</strong> {{ $json['emisor']['correo'] }}</div>
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
                            {{ $json['identificacion']['tipoDte'] === '14' ? 'FACTURA SUJETO EXCLUIDO' : '' }}
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
                        <p class="mb-1"><strong>CLIENTE:</strong> {{ $json['sujetoExcluido']['nombre'] }}</p>
                        <p class="mb-1"><strong>N°DOCUMENTO:</strong> {{ $json['sujetoExcluido']['numDocumento'] }}
                        </p>
                        <p class="mb-1"><strong>GIRO:</strong> {{ $json['sujetoExcluido']['descActividad'] }}</p>
                        <p class="mb-1"><strong>DIRECCIÓN:</strong>
                            {{ $json['sujetoExcluido']['direccion']['complemento'] }}</p>
                        <p class="mb-0"><strong>CORREO:</strong> {{ $json['sujetoExcluido']['correo'] }}</p>
                    </td>

                    <!-- Columna derecha: DETALLES -->
                    <td style="width: 50%; vertical-align: top; text-align: right; padding: 0.75rem;">
                        <p class="mb-1"><strong>FECHA:</strong> {{ $json['identificacion']['fecEmi'] }}
                            {{ $json['identificacion']['horEmi'] }}</p>
                        <p class="mb-1"><strong>FORMA DE PAGO:</strong>
                            {{ $json['resumen']['condicionOperacion'] == 1 ? 'CONTADO' : ($json['resumen']['condicionOperacion'] == 2 ? 'CRÉDITO' : 'DESCONOCIDO') }}
                        </p>
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
                        <th class="p-1">Codigo</th>
                        <th class="p-1">Descripción</th>
                        <th class="p-1">Valor Unitario</th>
                        <th class="p-1">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($json['cuerpoDocumento'] as $item)
                        <tr>
                            <td class="p-2">{{ $item['cantidad'] }}</td>
                            <td class="p-2">{{ $item['codigo'] }}</td>
                            <td class="p-2">{{ $item['descripcion'] }}</td>
                            <td class="p-2">${{ number_format($item['precioUni'], 2) }}</td>
                            <td class="p-2">${{ number_format($item['compra'], 2) }}</td>
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
                    <td
                        style="width: 75%; vertical-align: top; padding: 0.3rem 0.5rem; border-right: 1px solid black; text-align: left;">
                        <strong>Son:</strong><br> {{ $json['resumen']['totalLetras'] }}
                        <div style="border-bottom: 2px solid black; width: 101%; margin: 1rem 0 0 0;"></div>
                    </td>

                    <td style="width: 25%; vertical-align: top; padding: 0;">
                        <table class="table table-sm table-borderless mb-0"
                            style="font-size: 10px; border-collapse: collapse;">
                            <tbody>
                                <tr>
                                    <td style="border-right: 1px solid black; padding: 0.1rem 0.2rem;">Sumas</td>
                                    <td class="text-end" style="padding: 0.1rem 0.2rem;">
                                        ${{ number_format($json['resumen']['totalCompra'], 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-right: 1px solid black; padding: 0.1rem 0.2rem;">Sub-Total</td>
                                    <td class="text-end" style="padding: 0.1rem 0.2rem;">
                                        ${{ number_format($json['resumen']['subTotal'], 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-right: 1px solid black; padding: 0.1rem 0.2rem;">(-) Retención
                                        Renta</td>
                                    <td class="text-end" style="padding: 0.1rem 0.2rem;">
                                        ${{ number_format($json['resumen']['reteRenta'], 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="border-right: 1px solid black; font-weight: bold; padding: 0.1rem 0.2rem;">
                                        Total a Pagar
                                    </td>
                                    <td class="text-end fw-bold" style="padding: 0.1rem 0.2rem;">
                                        ${{ number_format($json['resumen']['totalPagar'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>


        @if (!empty($json['resumen']['observaciones']))
            <div class="mt-1"
                style="font-size: 9px; border: 1px solid black; border-radius: 6px; padding: 0.4rem; margin-top: 0.5rem;">
                <strong>Observaciones:</strong><br>
                {{ $json['resumen']['observaciones'] }}
            </div>
        @endif

        <div class="text-end mt-1" style="font-size: 8px; line-height: 1;">
            DTE emitido con software MoranZSoft One <br>
            Impreso por MoranZSoft One
        </div>
    </div>

</body>

</html>
