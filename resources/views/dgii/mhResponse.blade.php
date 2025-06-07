@extends('layouts.sneatTheme.base')
@section('title', 'MH respuesta')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom mb-3">
                        <h5 class="card-title mb-0">Ministerio de Hacienda Respuesta | {{ $documentoDTE->id }}.</h5>
                    </div>
                    <div class="card-body">
                        <div class="mt-3">
                            <a class="btn bg-label-primary filter-btn px-4 py-3 fw-semibold text-nowrap"
                                href="{{ route('facturacion.index') }}"><i class="icon-base bx bx-left-arrow-alt"></i>
                                Regresar</a>
                        </div>
                         <div class="rounded" style="color: #d4d4d4; padding: 1rem; overflow-x: auto;">
                            <h6 class="text-dark text-muted">Json Dte</h6>
                            <pre><code class="json">
{{ json_encode(json_decode($documentoDTE->json_dte, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
    </code></pre>
                        </div>
                        <h6 class="text-dark text-muted">Ministerio de Hacienda respuesta</h6>
                        <div class="rounded" style="color: #d4d4d4; padding: 1rem; overflow-x: auto;">
                            <pre><code class="json">
{{ json_encode(json_decode($documentoDTE->mh_response, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
    </code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
