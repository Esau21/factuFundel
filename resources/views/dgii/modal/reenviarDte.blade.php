<!-- Modal para reenviar DTE -->
<div class="modal fade" id="reenviarJson" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>

                <div class="text-center mb-4">
                    <h4 class="mb-2">Reenviar Documento Tributario Electrónico</h4>
                    <div class="alert alert-info fade show position-relative" role="alert">
                        <strong>Importante!</strong> Puedes editar el JSON antes de reenviar.
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                            data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>

                <form id="reenvioJsonDte" class="row g-3" onsubmit="return false;">
                    @csrf
                    <input type="hidden" name="documento_id" id="documento_id">

                    <div class="col-12">
                        <label for="json_dte" class="form-label">JSON del Documento</label>
                        <textarea id="json_dte" name="json_dte" class="form-control" rows="20"
                            placeholder="Aquí se cargará el JSON del DTE" required></textarea>
                    </div>

                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary me-2">Reenviar Documento</button>
                        <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal"
                            aria-label="Cerrar">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Spinner -->
<div class="modal fade" id="spinnerModalReenvio" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div
            class="modal-content bg-light border-0 shadow-lg rounded-4 text-center p-4 d-flex flex-column align-items-center justify-content-center">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h5 class="text-dark fw-semibold text-center">Reenviando DTE, por favor espere...</h5>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        // Abrir modal y cargar JSON vía AJAX
        $(document).on('click', '.btn-reenvio-json', function() {
            let documentoId = $(this).data('id');
            let url = `/facturacion/obtener-json/${documentoId}`;

            $('#documento_id').val(documentoId);
            $('#json_dte').val(''); // Limpiar mientras carga

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    $('#json_dte').val(JSON.stringify(response.json, null,
                    4)); // Formato bonito
                    $('#reenviarJson').modal('show'); // Mostrar modal cuando cargue el JSON
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo cargar el JSON del documento.', 'error');
                }
            });
        });

        // Enviar el formulario por AJAX
        $('#reenvioJsonDte').on('submit', function(e) {
            e.preventDefault();

            let id = $('#documento_id').val();
            let jsonDte = $('#json_dte').val();
            let token = $('input[name="_token"]').val();

            if (!jsonDte.trim()) {
                Swal.fire('El JSON no puede estar vacío', '', 'warning');
                return;
            }

            let url = `/reenviar/json/dte/${id}`;

            $('#spinnerModalReenvio').modal('show');

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: token,
                    json_dte: jsonDte
                },
                success: function(response) {
                    $('#spinnerModalReenvio').modal('hide');
                    $('#reenviarJson').modal('hide');

                    if (response.success) {
                        Toastify({
                            text: response.message,
                            className: "success",
                            style: {
                                background: "linear-gradient(to right, #28a745, #218838)"
                            }
                        }).showToast();
                    } else {
                        Swal.fire('Error al reenviar', response.message, 'error');
                    }

                    $('#sysconta-datatable').DataTable().ajax.reload(null, false);
                },
                error: function(e) {
                    $('#spinnerModalReenvio').modal('hide');

                    let mensaje = e.responseJSON?.message || 'Error desconocido';
                    Swal.fire('Error al reenviar', mensaje, 'error');
                }
            });
        });
    });
</script>
