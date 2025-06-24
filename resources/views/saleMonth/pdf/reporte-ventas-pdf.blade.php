<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de ventas </title>


    <link href="{{ public_path('css/custom_pdf.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_path('css/custom_page.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>
    <section class="header" style="top:-250px;">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="30%" style="vertical-align:top; padding-top:10px; position:relative;">
                    <img src="{{ empresaLogo() }}" alt="Logo" class="img-fluid" width="130">
                </td>
            </tr>
        </table>
    </section>

    <div class="changes" style="margin-top:-250px;">
        <table cellpadding="0" cellspacing="0" width="100%">

            <tr>
                <td colspan="2" align="center">
                    <span style="font-size: 25px; font-weight:bold;">Sistema de gestión de ventas</span>
                </td>
            </tr>
            <tr>
                <td width="70%" class="text-left text-company"
                    style="vertical-align: top; padding-top: 10px; padding-left: 150px;">



                    <span style="font-size: 16px;"><strong>Fecha del reporte:
                            {{ \Carbon\Carbon::now()->format('d-m-Y') }}</strong></span>

                    <br>
                    @if ($cliente)
                        <span style="font-size: 14px;"><strong>Cliente:</strong>
                            {{ $cliente->nombre ?? '' }}</span>
                        <br>
                    @endif
                    <span style="font-size: 14px;"><strong>Usuario:</strong> {{ Auth::user()->name }}</span>
                    <br>
                    @if ($fechaInicio)
                        <span style="font-size: 14px;"><strong>Desde:</strong>
                            {{ $fechaInicio }}</span>
                    @endif
                    <br>
                    @if ($fechaFin)
                        <span style="font-size: 14px;"><strong>Hasta:</strong>
                            {{ $fechaFin }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>



    <section style="margin-top: -20px; margin-bottom: 35px;">
        <table cellpadding="0" cellspacing="0" width="100%" class="table-items2">
            <thead>
                <tr>
                    <th align="center">ID</th>
                    <th align="center">N° Documento</th>
                    <th align="center">Cliente</th>
                    <th align="center">Usuario</th>
                    <th align="center">Fecha Pago</th>
                    <th align="center">Tipo Pago</th>
                    <th align="center">Total</th>
                    <th align="center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ventas as $venta)
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>{{ $venta->documentoDte->numero_control ?? 'Sin dato' }}</td>
                        <td>{{ $venta->clientes->nombre ?? '' }}</td>
                        <td>{{ $venta->users->name ?? '' }}</td>
                        <td>{{ $venta->fecha_venta }}</td>
                        <td>
                            @if ($venta->tipo_pago == '01')
                                <span class="badge badge-center rounded-pill bg-label-primary me-1"><i
                                        class="icon-base bx bx-money"></i>Efectivo</span>
                            @elseif($venta->tipo_pago == '04')
                                <span class="badge badge-center rounded-pill bg-label-success me-1"><i
                                        class="icon-base bx bx-receipt"></i></span> Cheque
                            @elseif($venta->tipo_pago == '05')
                                <span class="badge badge-center rounded-pill bg-label-danger me-1"><i
                                        class="icon-base bx bx-transfer"></i></span> Transferencia
                            @else
                                <span class="badge badge-center rounded-pill bg-label-secondary me-1"><i
                                        class="icon-base bx bx-help-circle"></i></span> Otro
                            @endif
                        </td>
                        <td>${{ number_format($venta->total + $venta->iva - $venta->retencion, 2) }}</td>
                        <td>
                            @if ($venta->status == 'PAID')
                                <span class="badge badge-center rounded-pill bg-label-success me-1">
                                    <i class="icon-base bx bx-check-circle"></i>
                                </span> Pagado
                            @elseif($venta->status == 'PENDING')
                                <span class="badge badge-center rounded-pill bg-label-warning me-1">
                                    <i class="icon-base bx bx-time-five"></i>
                                </span> Pendiente
                            @elseif($venta->status == 'CANCEL')
                                <span class="badge badge-center rounded-pill bg-label-danger me-1">
                                    <i class="icon-base bx bx-x-circle"></i>
                                </span> Cancelado
                            @else
                                <span class="badge badge-center rounded-pill bg-label-secondary me-1">
                                    <i class="icon-base bx bx-help-circle"></i>
                                </span> Desconocido
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>



    <section class="footer">
        <table cellpadding="0" cellspacing="0" width="100%" class="table-items">
            <tr>
                <td width="20%"><span> Dirección: COLONIA ESCALON CALLE AL MIRADOR</span></td>
                <td width="60%" align="center">Reporte de ventas</td>
                <td width="20%" align="center">Pagina <span class="pagenum"></span></td>
            </tr>
        </table>
    </section>

</body>

</html>
