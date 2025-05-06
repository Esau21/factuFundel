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

                                <!-- Giro -->
                                <div class="col-12 col-md-3 form-control-validation mb-3">
                                    <label class="form-label w-100" for="giro">Giro</label>
                                    <div class="input-group input-group-merge">
                                        <input id="giro" name="giro" class="form-control" type="text"
                                            placeholder="ejemplo" value="{{ $cliente->giro ?? 'sin giro' }}" />
                                        <span class="input-group-text cursor-pointer"><span
                                                class="card-type me-n2"></span></span>
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
                                    <div class="input-group input-group-merge">
                                        <select name="departamento" id="departamento" class="form-control select2"
                                            required>
                                        </select>
                                    </div>
                                </div>

                                <!-- Municipio -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="municipio">Municipio</label>
                                    <div class="input-group input-group-merge">
                                        <select name="municipio" id="municipio" class="form-control select2" required>

                                        </select>
                                    </div>
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

                                <!-- Codigo de actividad -->
                                <div class="col-12 col-md-6 form-control-validation mb-3">
                                    <label class="form-label w-100" for="codigo_actividad">Codigo de actividad</label>
                                    <div class="input-group input-group-merge">
                                        <input id="codigo_actividad" name="codigo_actividad" class="form-control"
                                            type="text" placeholder="ejemplo" />
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
    const clienteDepartamento = @json($departamento);
    const clienteMunicipio = @json($municipio);
</script>

<script>
    $(document).ready(function() {

        const $departamento = $("#departamento");
        const $municipio = $("#municipio");

        /* limipiamos y llenamos los departamentos y municipios */
        $departamento.empty().append('<option value="">Elegir</option>');
        Object.keys(departamentosMunicipios).forEach(dep => {
            $departamento.append(`<option value="${dep}">${dep}</option>`);
        });

        /* cuando cambiemos de departamento cambiamos de municipio */
        $departamento.on("change", function() {
            const selectedDep = $(this).val();
            const municipios = departamentosMunicipios[selectedDep] || [];

            $municipio.empty().append('<option value="">Elegir</option>');
            municipios.forEach(muni => {
                const selected = (selectedDep === clienteDepartamento && muni ===
                    clienteMunicipio) ? 'selected' : '';
                $municipio.append(`<option value="${muni}" ${selected}>${muni}</option>`);
            });
        });

        /* Si venimos de modo edición, precargamos valores */
        if (clienteDepartamento) {
            $departamento.val(clienteDepartamento).trigger('change');
        }

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

<script>
    const departamentosMunicipios = {
        "Ahuachapán": ["Ahuachapán", "Apaneca", "Atiquizaya", "Concepción de Ataco", "El Refugio", "Guaymango",
            "Jujutla", "San Francisco Menéndez", "San Lorenzo", "San Pedro Puxtla", "Tacuba", "Turín"
        ],
        "Santa Ana": ["Candelaria de la Frontera", "Chalchuapa", "Coatepeque", "El Congo", "El Porvenir",
            "Masahuat", "Metapán", "San Antonio Pajonal", "San Sebastián Salitrillo", "Santa Ana",
            "Santa Rosa Guachipilín", "Santiago de la Frontera", "Texistepeque"
        ],
        "Sonsonate": ["Acajutla", "Armenia", "Caluco", "Cuisnahuat", "Izalco", "Juayúa", "Nahuizalco", "Nahulingo",
            "Salcoatitán", "San Antonio del Monte", "San Julián", "Santa Catarina Masahuat",
            "Santa Isabel Ishuatán", "Santo Domingo de Guzmán", "Sonsonate"
        ],
        "La Libertad": ["Antiguo Cuscatlán", "Chiltiupán", "Ciudad Arce", "Colón", "Comasagua", "Huizúcar",
            "Jayaque", "Jicalapa", "La Libertad", "Nuevo Cuscatlán", "Opico", "Quezaltepeque", "Sacacoyo",
            "San Juan Opico", "San Matías", "San Pablo Tacachico", "Santa Tecla", "Talnique", "Tamanique",
            "Teotepeque", "Tepecoyo", "Zaragoza"
        ],
        "Chalatenango": ["Agua Caliente", "Arcatao", "Azacualpa", "Cancasque", "Chalatenango", "Citalá", "Comalapa",
            "Concepción Quezaltepeque", "Dulce Nombre de María", "El Carrizal", "El Paraíso", "La Laguna",
            "La Palma", "La Reina", "Las Vueltas", "Nombre de Jesús", "Nueva Concepción", "Nueva Trinidad",
            "Ojos de Agua", "Potonico", "San Antonio de la Cruz", "San Antonio Los Ranchos", "San Fernando",
            "San Francisco Lempa", "San Francisco Morazán", "San Ignacio", "San Isidro Labrador",
            "San Luis del Carmen", "San Miguel de Mercedes", "San Rafael", "Santa Rita", "Tejutla"
        ],
        "Cuscatlán": ["Candelaria", "Cojutepeque", "El Carmen", "El Rosario", "Monte San Juan",
            "Oratorio de Concepción", "San Bartolomé Perulapía", "San Cristóbal", "San José Guayabal",
            "San Pedro Perulapán", "San Rafael Cedros", "San Ramón", "Santa Cruz Analquito",
            "Santa Cruz Michapa", "Suchitoto", "Tenancingo"
        ],
        "San Vicente": ["Apastepeque", "Guadalupe", "San Cayetano Istepeque", "San Esteban Catarina",
            "San Ildefonso", "San Lorenzo", "San Sebastián", "San Vicente", "Santa Clara", "Santo Domingo",
            "Tecoluca", "Tepetitán", "Verapaz"
        ],
        "Cabañas": ["Cinquera", "Dolores", "Guacotecti", "Ilobasco", "Jutiapa", "San Isidro", "Sensuntepeque",
            "Tejutepeque", "Victoria"
        ],
        "La Paz": ["Cuyultitán", "El Rosario", "Jerusalén", "Mercedes La Ceiba", "Olocuilta", "Paraíso de Osorio",
            "San Antonio Masahuat", "San Emigdio", "San Francisco Chinameca", "San Juan Nonualco",
            "San Juan Talpa", "San Juan Tepezontes", "San Luis La Herradura", "San Luis Talpa",
            "San Miguel Tepezontes", "San Pedro Masahuat", "San Pedro Nonualco", "San Rafael Obrajuelo",
            "Santa María Ostuma", "Santiago Nonualco", "Tapalhuaca", "Zacatecoluca"
        ],
        "San Salvador": ["Aguilares", "Apopa", "Ayutuxtepeque", "Cuscatancingo", "Delgado", "El Paisnal", "Guazapa",
            "Ilopango", "Mejicanos", "Nejapa", "Panchimalco", "Rosario de Mora", "San Marcos", "San Martín",
            "San Salvador", "Santiago Texacuangos", "Santo Tomás", "Soyapango", "Tonacatepeque"
        ],
        "La Unión": ["Anamorós", "Bolívar", "Concepción de Oriente", "Conchagua", "El Carmen", "El Sauce",
            "Intipucá", "La Unión", "Lislique", "Meanguera del Golfo", "Nueva Esparta", "Pasaquina", "Polorós",
            "San Alejo", "San José", "Santa Rosa de Lima", "Yayantique", "Yucuaiquín"
        ],
        "San Miguel": ["Carolina", "Chapeltique", "Chinameca", "Chirilagua", "Ciudad Barrios", "Comacarán",
            "El Tránsito", "Lolotique", "Moncagua", "Nueva Guadalupe", "Nuevo Edén de San Juan", "Quelepa",
            "San Antonio", "San Gerardo", "San Jorge", "San Luis de la Reina", "San Miguel",
            "San Rafael Oriente", "Sesori", "Uluazapa"
        ],
        "Morazán": ["Arambala", "Cacaopera", "Chilanga", "Corinto", "Delicias de Concepción", "El Divisadero",
            "El Rosario", "Gualococti", "Guatajiagua", "Joateca", "Jocoaitique", "Jocoro", "Lolotiquillo",
            "Meanguera", "Osicala", "Perquín", "San Carlos", "San Fernando", "San Francisco Gotera",
            "San Isidro", "San Simón", "Sensembra", "Sociedad", "Torola", "Yamabal", "Yoloaiquín"
        ],
        "Usulután": ["Alegría", "Berlín", "California", "Concepción Batres", "El Triunfo", "Ereguayquín",
            "Estanzuelas", "Jiquilisco", "Jucuapa", "Jucuarán", "Mercedes Umaña", "Nueva Granada", "Ozatlán",
            "Puerto El Triunfo", "San Agustín", "San Buenaventura", "San Dionisio", "San Francisco Javier",
            "Santa Elena", "Santa María", "Santiago de María", "Tecapán", "Usulután"
        ]
    }
</script>
