<!-- Modal para enviar evento de contingencia -->
<div class="modal fade" id="sendContingencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>

                <div class="text-center mb-4">
                    <h4 class="mb-2">Enviar Evento de Contingencia</h4>
                    <div class="alert alert-warning fade show" role="alert">
                        <strong>Importante:</strong> Este formulario notifica un evento de contingencia ocurrido durante
                        la emisión de documentos.
                    </div>
                </div>

                <form id="sendEventoConti" class="row g-3" onsubmit="return false;">
                    @csrf
                    <input type="hidden" name="documento_id" id="documento_id">

                    <div class="col-md-6">
                        <label for="fInicio" class="form-label">Fecha inicio</label>
                        <input id="fInicio" name="fInicio" class="form-control" type="date" required>
                    </div>

                    <div class="col-md-6">
                        <label for="hInicio" class="form-label">Hora inicio</label>
                        <input id="hInicio" name="hInicio" class="form-control" type="time" required>
                    </div>

                    <div class="col-md-6">
                        <label for="fFin" class="form-label">Fecha fin</label>
                        <input id="fFin" name="fFin" class="form-control" type="date" required>
                    </div>

                    <div class="col-md-6">
                        <label for="hFin" class="form-label">Hora fin</label>
                        <input id="hFin" name="hFin" class="form-control" type="time" required>
                    </div>

                    <div class="col-md-6">
                        <label for="tipoContingencia" class="form-label">Tipo de contingencia</label>
                        <select id="tipoContingencia" name="tipoContingencia" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="1">No disponibilidad de sistema del MH</option>
                            <option value="2">No disponibilidad de sistema del emisor</option>
                            <option value="3">Falla en el suministro de servicio de internet del Emisor</option>
                            <option value="4">Falla en el suministro de servicio de energía eléctrica del emisor
                                que impida la transmisión de los DTE</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="motivoContingencia" class="form-label">Motivo (opcional)</label>
                        <textarea id="motivoContingencia" name="motivoContingencia" class="form-control" rows="2"
                            placeholder="Ej. Corte de energía prolongado"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="nombre_responsable" class="form-label">Nombre del responsable</label>
                        <input id="nombre_responsable" name="nombre_responsable" class="form-control" type="text"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="tipo_doc_responsable" class="form-label">Tipo de documento</label>
                        <select id="tipo_doc_responsable" name="tipo_doc_responsable" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <option value="13">DUI</option>
                            <option value="36">NIT</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="num_doc_responsable" class="form-label">Número de documento</label>
                        <input id="num_doc_responsable" name="num_doc_responsable" class="form-control" type="text"
                            required>
                    </div>

                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary me-2">Enviar evento</button>
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .swal2-container {
        z-index: 20000 !important;
        /* Para mostrar Swal sobre el modal */
    }
</style>


<!-- Modal Spinner (debe estar fuera del modal principal) -->
<div class="modal fade" id="spinnerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div
            class="modal-content bg-light border-0 shadow-lg rounded-4 text-center p-4 d-flex flex-column align-items-center justify-content-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h5 class="text-dark fw-semibold text-center">Enviando contingencia, por favor espere...</h5>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        let id = null;

        $(document).on('click', '.btn-send-contingencia', function() {
            id = $(this).data('id');
            const tipoTransmision = $(this).data('transmision');

            $('#documento_id').val(id);
            $('#tipoTransmisionInput').val(tipoTransmision == 2 ? 'Contingencia' : 'En línea');

            // Limpiar el formulario
            $('#sendEventoConti')[0].reset();

            // Mostrar modal
            $('#sendContingencia').modal('show');
        });

        $('#sendEventoConti').on('submit', function(e) {
            e.preventDefault();

            const fInicio = $('#fInicio').val();
            const fFin = $('#fFin').val();

            const today = new Date().toISOString().split('T')[0];

            // Validación de fechas futuras
            if (fInicio > today || fFin > today) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fecha inválida',
                    text: 'No puedes seleccionar fechas futuras para un evento de contingencia.'
                });
                return;
            }

            // Validación de orden de fechas
            if (fInicio > fFin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas inválidas',
                    text: 'La fecha de fin no puede ser anterior a la fecha de inicio.'
                });
                return;
            }

            // Mostrar spinner y ocultar modal
            $('#sendContingencia').modal('hide');
            $('#spinnerModal').modal('show');

            const formData = $(this).serialize();

            $.ajax({
                url: '/facturacion/emitir/contingencia/documento/' + id,
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#spinnerModal').modal('hide');

                    if (response.data.estado === 'RECIBIDO') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Evento registrado!',
                            text: response.data.mensaje,
                            footer: 'Sello recibido: ' + response.data.selloRecibido
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Evento rechazado',
                            text: response.data.mensaje,
                            footer: (response.data.observaciones || []).join(
                                '<br>') || 'Verifica los datos.'
                        });
                    }
                },
                error: function() {
                    $('#spinnerModal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al enviar',
                        text: 'No se pudo procesar el evento de contingencia.'
                    });
                }
            });
        });
    });
</script>
