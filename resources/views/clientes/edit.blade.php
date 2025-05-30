@extends('layouts.sneatTheme.base')
@section('title', 'Editar cliente')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom mb-3">
                        <h5 class="card-title mb-0">Editar detalle del cliente | {{ $cliente->nombre }}.</h5>
                    </div>
                    <div class="card-body">
                        <form id="UpdatedCliente" class="row g-6" onsubmit="return false">
                            @csrf
                            <!-- Tipo de persona -->
                            <div class="col-12 col-md-6 form-control-validation mb-3">
                                <label class="form-label w-100" for="tipo_persona">Tipo de persona</label>
                                <div class="input-group input-group-merge">
                                    <select name="tipo_persona" id="tipo_persona" class="form-control" required>
                                        <option value="">Elegir</option>
                                        <option value="natural" {{ $cliente->tipo_persona == 'natural' ? 'selected' : '' }}>
                                            Natural</option>
                                        <option value="juridica"
                                            {{ $cliente->tipo_persona == 'juridica' ? 'selected' : '' }}>Juridica</option>
                                    </select>
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <div class="row" id="mostrarform" style="display: none;">
                                <!-- Nombre cliente -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nombre">Nombre cliente</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nombre" name="nombre" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $cliente->nombre }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nombreCo">Nombre Comercial</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nombreComercial" name="nombreComercial" class="form-control"
                                            type="text" placeholder="ejemplo" value="{{ $cliente->nombreComercial }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Tipo de documento -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="tipo_documento">Tipo de documento</label>
                                    <div class="input-group input-group-merge">
                                        <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                                            <option value="">Elegir</option>
                                            <option value="DUI"
                                                {{ $cliente->tipo_documento == 'DUI' ? 'selected' : '' }}>DUI</option>
                                            <option value="NIT"
                                                {{ $cliente->tipo_documento == 'NIT' ? 'selected' : '' }}>NIT</option>
                                            <option value="PASAPORTE"
                                                {{ $cliente->tipo_documento == 'PASAPORTE' ? 'selected' : '' }}>Pasaporte
                                            </option>
                                            <option value="CEDULA"
                                                {{ $cliente->tipo_documento == 'CEDULA' ? 'selected' : '' }}>Cédula</option>
                                        </select>
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Numero de documento -->
                                <div class="col-12 col-md-4 form-control-validation mb-3">
                                    <label class="form-label w-100" for="numero_documento">Numero de documento</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numero_documento" name="numero_documento" class="form-control"
                                            type="text" placeholder="ejemplo" value="{{ $cliente->numero_documento }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Nrc -->
                                <div class="col-12 col-md-4 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nrc">Nrc</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nrc" name="nrc" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $cliente->nrc ?? 'sin datos' }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Nit -->
                                <div class="col-12 col-md-4 form-control-validation mb-3">
                                    <label class="form-label w-100" for="nit">Nit</label>
                                    <div class="input-group input-group-merge">
                                        <input id="nit" name="nit" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $cliente->nit ?? 'sin datos' }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="actividad_economica_id">Actividad
                                        economica</label>
                                    <div class="input-group input-group-merge">
                                        <select name="actividad_economica_id" id="actividad_economica_id"
                                            class="form-select select2 w-100">
                                            <option value="">Seleccionar</option>
                                            @foreach ($actividad as $a)
                                                <option value="{{ $a->id }}"
                                                    {{ old('actividad_economica_id', $cliente->actividad_economica_id ?? '') == $a->id ? 'selected' : '' }}>
                                                    {{ $a->codActividad }} | {{ $a->descActividad }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Direccion -->
                                <div class="col-12 col-md-9 form-control-validation mb-3">
                                    <label class="form-label w-100" for="direccion">Direccion</label>
                                    <div class="input-group input-group-merge">
                                        <textarea name="direccion" id="direccion" cols="30" rows="1" class="form-control"
                                            placeholder="Ingresa tu descripcion">{{ old('direccion', $cliente->direccion) }}</textarea>
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Departamento -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="departamento">Departamento</label>
                                    <select name="departamento_id" id="departamento_id" class="form-control select2"
                                        required>
                                        <option value="">Elegir</option>
                                        @foreach ($departamentos as $d)
                                            <option value="{{ $d->id }}"
                                                {{ old('departamento_id', $cliente->departamento_id) == $d->id ? 'selected' : '' }}>
                                                {{ $d->codigo }} | {{ $d->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Municipio -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="municipio">Municipio</label>
                                    <select name="municipio_id" id="municipio_id" class="form-control select2" required>
                                        <option value="">Elegir</option>
                                        @foreach ($municipios as $m)
                                            <option value="{{ $m->id }}"
                                                {{ old('municipio_id', $cliente->municipio_id) == $m->id ? 'selected' : '' }}>
                                                {{ $m->codigo }} | {{ $m->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Telefono -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="telefono">Telefono</label>
                                    <div class="input-group input-group-merge">
                                        <input id="telefono" name="telefono" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $cliente->telefono ?? 'sin tel' }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Correo electronico -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="correo_electronico">Correo electronico</label>
                                    <div class="input-group input-group-merge">
                                        <input id="correo_electronico" name="correo_electronico" class="form-control"
                                            type="text" placeholder="ejemplo"
                                            value="{{ $cliente->correo_electronico ?? 'sin correo' }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>

                                <!-- Tipo Contribuyente -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="tipo_contribuyente">Tipo Contribuyente</label>
                                    <div class="input-group input-group-merge">
                                        <select name="tipo_contribuyente" id="tipo_contribuyente" class="form-control"
                                            required>
                                            <option value="">Elegir</option>
                                            <option value="contribuyente"
                                                {{ $cliente->tipo_contribuyente == 'contribuyente' ? 'selected' : '' }}>
                                                Contribuyente</option>
                                            <option value="gubernamental"
                                                {{ $cliente->tipo_contribuyente == 'gubernamental' ? 'selected' : '' }}>
                                                Gubernamental</option>
                                            <option value="exento"
                                                {{ $cliente->tipo_contribuyente == 'exento' ? 'selected' : '' }}>Exento
                                            </option>
                                            <option value="consumidor_final"
                                                {{ $cliente->tipo_contribuyente == 'consumidor_final' ? 'selected' : '' }}>
                                                Consumidor Final</option>
                                        </select>
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
                                    </div>
                                </div>


                                <!-- Botones de acción -->
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Guardar</button>
                                    <a href="{{ route('clientes.index') }}"
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
        function toggleFields(tipo) {
            if (tipo === 'natural' || tipo === 'juridica') {
                $('#mostrarform').show();

                if (tipo === 'natural') {
                    $('#nrc').closest('.form-control-validation').hide();
                    $('#giro').closest('.form-control-validation').hide();
                    $('#codigo_actividad').closest('.form-control-validation').hide();
                } else {
                    $('#nrc').closest('.form-control-validation').show();
                    $('#giro').closest('.form-control-validation').show();
                    $('#codigo_actividad').closest('.form-control-validation').show();
                }
            } else {
                $('#mostrarform').hide();
            }
        }

        toggleFields($('#tipo_persona').val());

        $('#tipo_persona').on('change', function() {
            toggleFields($(this).val());
        });

        $("#UpdatedCliente").on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            var btnSubmit = $(form).find('button[type="submit"]');
            btnSubmit.prop('disabled', true);
            let url = "{{ route('clientes.update', $cliente->id) }}";

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                },
                processData: false,
                contentType: false,
                success: function() {
                    Toastify({
                        text: "El cliente se modifico correctamente.",
                        className: "info",
                        style: {
                            background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                        }
                    }).showToast();
                    setTimeout(() => {
                        window.location.href = "{{ route('clientes.index') }}";
                    }, 2000);
                },
                error: function(e) {
                    if (e.status === 422) {
                        let errors = e.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value.join('<br>');
                        });
                        Toastify({
                            text: "Errores de validación:\n" + errorMessage.replace(
                                /<br>/g, '\n'),
                            duration: 5000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #e74c3c, #c0392b)",
                            }
                        }).showToast();
                    } else if (e.status === 405) {
                        Toastify({
                            text: e.responseJSON.error || "Método no permitido.",
                            duration: 5000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #e67e22, #d35400)",
                            }
                        }).showToast();
                    } else {
                        Toastify({
                            text: "Algo salió mal al insertar los datos.",
                            duration: 5000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "linear-gradient(to right, #e74c3c, #c0392b)",
                            }
                        }).showToast();
                    }
                }
            });

        });
    });
</script>
