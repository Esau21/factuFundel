@extends('layouts.sneatTheme.base')
@section('title', 'Editar empresa')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom mb-3">
                        <h5 class="card-title mb-0">Editar empresa | {{ $empresa->nombre }}.</h5>
                    </div>
                    <div class="card-body mt-5">
                        <form id="updateEmpresa" class="row g-6" onsubmit="return false" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <!-- Imagen -->
                                <div class="col-12 form-control-validation">
                                    <label class="form-label w-100" for="imagen">Imagen</label>
                                    <div class="input-group input-group-merge">
                                        <input id="logo" name="logo" class="form-control" type="file" />
                                        <span class="input-group-text cursor-pointer">
                                            <span class="card-type me-n2"></span>
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <img id="logo-preview"
                                            src="{{ !empty($empresa->logo) && file_exists(public_path('storage/' . $empresa->logo))
                                                ? asset('storage/' . $empresa->logo)
                                                : asset('img/camara1.png') }}"
                                            alt="Logo de la empresa" style="max-height: 150px;">
                                    </div>
                                </div>

                                <!-- Nombre empresa -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nombre">Nombre empresa</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nombre" name="nombre" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $empresa->nombre }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nombreCo">Nombre Comercial</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nombreComercial" name="nombreComercial" class="form-control"
                                            type="text" placeholder="ejemplo" value="{{ $empresa->nombreComercial }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Nrc -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nrc">Nrc</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nrc" name="nrc" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $empresa->nrc }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Nit -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nit">Nit</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nit" name="nit" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $empresa->nit }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="departamento_id">Departamento</label>
                                    <div class="input-group input-group-merge">
                                        <select name="departamento_id" id="departamento_id"
                                            class="form-select select2 w-100">
                                            <option value="">Seleccionar</option>
                                            @foreach ($departamento as $d)
                                                <option value="{{ $d->id }}"
                                                    {{ old('departamento_id', $empresa->departamento_id ?? '') == $d->id ? 'selected' : '' }}>
                                                    {{ $d->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="municipio_id">Municipio</label>
                                    <div class="input-group input-group-merge">
                                        <select name="municipio_id" id="municipio_id" class="form-select select2 w-100">
                                            <option value="">Seleccionar</option>
                                            @foreach ($municipio as $d)
                                                <option value="{{ $d->id }}"
                                                    {{ old('municipio_id', $empresa->municipio_id ?? '') == $d->id ? 'selected' : '' }}>
                                                    {{ $d->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="actividad_economica_id">Actividad economica</label>
                                    <div class="input-group input-group-merge">
                                        <select name="actividad_economica_id" id="actividad_economica_id" class="form-select select2 w-100">
                                            <option value="">Seleccionar</option>
                                            @foreach ($actividad as $a)
                                                <option value="{{ $a->id }}"
                                                    {{ old('actividad_economica_id', $empresa->actividad_economica_id ?? '') == $a->id ? 'selected' : '' }}>
                                                    {{ $a->codActividad }} | {{ $a->descActividad }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <!-- Direccion -->
                                <div class="col-12 col-md-12 form-control-validation mb-3">
                                    <label class="form-label w-100" for="complemento">Complemento</label>
                                    <div class="input-group input-group-merge">
                                        <textarea name="complemento" id="complemento" cols="30" rows="1" class="form-control"
                                            placeholder="Ingresa tu direccion">{{ $empresa->complemento }}</textarea>
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>



                                <!-- Telefono -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="telefono">Telefono</label>
                                    <div class="input-group input-group-merge">
                                        <input id="telefono" name="telefono" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $empresa->telefono }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Correo electronico -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="correo_electronico">Correo electronico</label>
                                    <div class="input-group input-group-merge">
                                        <input id="correo" name="correo" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $empresa->correo }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Guardar</button>
                                    <a href="{{ route('empresas.index') }}"
                                        class="btn btn-label-secondary btn-reset">Regresar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script>
    $(document).ready(function() {

        $('#logo').on('change', function(event) {
            const input = event.target;

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#logo-preview').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        });

        $("#updateEmpresa").on('submit', function(e) {
            e.preventDefault();
            let url = "{{ route('empresas.update', $empresa->id) }}";
            const form = this;
            const formData = new FormData(form);
            var btnSubmit = $(form).find('button[type="submit"]');
            btnSubmit.prop('disabled', true);
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                },
                success: function() {
                    Toastify({
                        text: "La empresa se actualizo correctamente.",
                        className: "info",
                        style: {
                            background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                        }
                    }).showToast();
                    setTimeout(() => {
                        window.location.href = "{{ route('empresas.index') }}";
                    }, 2000);
                },
                error: function(e) {
                    if (e.status === 422) {
                        let errors = e.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value.join('<br>');
                        });
                        Swal.fire({
                            title: 'Errores de validación.',
                            html: errorMessage,
                            icon: 'error',
                        });
                        setTimeout(() => {
                            window.location.href = "{{ route('empresas.index') }}";
                        }, 2000);
                    } else if (e.status === 405) {
                        Swal.fire({
                            title: 'Error',
                            text: e.responseJSON.error,
                            icon: 'error',
                        });
                    } else {
                        Swal.fire({
                            title: 'Algo salió mal al insertar los datos.',
                            icon: 'error'
                        });
                    }
                }
            });
        });

    });
</script>
