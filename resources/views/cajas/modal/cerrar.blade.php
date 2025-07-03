<div class="modal fade" id="cerrarCajaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
        <div class="modal-content">
            <form id="formCerrarCaja" method="POST">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Caja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="caja_id" id="modal_caja_id">
                    <!-- Campos: efectivo, tarjeta, otros, declarado -->
                    <div class="col-12">
                        <label>Monto en Efectivo</label>
                        <input type="number" name="total_efectivo" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label>Monto con Tarjeta</label>
                        <input type="number" name="total_tarjeta" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label>Otros Ingresos</label>
                        <input type="number" name="total_otros" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label>Total Declarado</label>
                        <input type="number" name="total_declarado" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Cerrar Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {

        // 👉 Al hacer clic en el botón de cerrar caja
        $(document).on('click', '.btn-cerrar-caja', function() {
            let cajaId = $(this).data('id');
            let actionUrl = '/cajas/' + cajaId + '/cerrar';

            // Setear la URL en el form
            $('#formCerrarCaja').attr('action', actionUrl);

            // Guardar también el ID en un campo oculto si lo necesitas
            $('#modal_caja_id').val(cajaId);
        });

        // 👉 Al enviar el formulario para cerrar la caja
        $('#formCerrarCaja').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let actionUrl = form.attr('action'); // ya viene desde el modal
            let btnSubmit = form.find('button[type="submit"]');

            btnSubmit.prop('disabled', true);

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                },
                success: function(response) {
                    $('#cerrarCajaModal').modal('hide');
                    form[0].reset();
                    btnSubmit.prop('disabled', false);

                    // Recargar datatable
                    var table = $('#sysconta-datatable').DataTable();
                    table.ajax.reload(null, false);

                    // Notificación de éxito
                    Toastify({
                        text: response.message || "Caja cerrada correctamente.",
                        className: "info",
                        style: {
                            background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                        }
                    }).showToast();
                },
                error: function(e) {
                    $('#cerrarCajaModal').modal('hide');
                    form[0].reset();
                    btnSubmit.prop('disabled', false);

                    if (e.status === 422) {
                        let errors = e.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value.join('<br>');
                        });
                        Swal.fire({
                            title: 'Errores de validación',
                            html: errorMessage,
                            icon: 'error',
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: e.responseJSON.message ||
                                'Ocurrió un error al cerrar la caja.',
                            icon: 'error',
                        });
                    }
                }
            });
        });
    });
</script>
