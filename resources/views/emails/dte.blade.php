@component('mail::message')
    <div style="text-align: center; padding-bottom: 20px;">
        <img src="{{ $message->embed(public_path('storage/' . auth()->user()->empresa->logo ?? 'img/camara1.png')) }}"
            alt="Logo Empresa" style="max-width: 150px; height: auto; display: inline-block;" />
    </div>

    ENVÍO DE DOCUMENTO TRIBUTARIO ELECTRÓNICO

    Estimado cliente: {{ $venta->clientes->nombre ?? 'Cliente' }},

    Adjunto encontrará su Documento Tributario Electrónico (DTE) correspondiente a su compra reciente.

    Si tiene alguna duda o requiere asistencia, no dude en contactarnos.

    Gracias por confiar en nosotros.

    Saludos cordiales,
    **{{ config('app.name') }}**

    @slot('footer')
        <div style="font-size: 12px; color: #888; text-align: center; margin-top: 30px;">
            © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
        </div>
    @endslot
@endcomponent
