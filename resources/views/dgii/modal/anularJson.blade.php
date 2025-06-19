 {{-- modal --}}
 <!-- Modal principal para anular documento -->
 <div class="modal fade" id="anularJson" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl">
         <div class="modal-content">
             <div class="modal-body position-relative">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Cerrar"></button>

                 <div class="text-center mb-4">
                     <h4 class="mb-2">Anular Documento Tributario Electrónico</h4>
                     <div class="alert alert-danger fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>

                 <form id="anularDTEMH" class="row g-3" onsubmit="return false;">
                     @csrf
                     <input type="hidden" name="documento_id" id="documento_id">

                     <!-- Campos del formulario -->
                     <div class="col-md-6">
                         <label for="tipo_documento" class="form-label">Tipo de documento</label>
                         <input id="tipo_documento" name="tipo_documento" class="form-control" type="text"
                             readonly />
                     </div>

                     <div class="col-md-6">
                         <label for="numero_control" class="form-label">Número de control</label>
                         <input id="numero_control" name="numero_control" class="form-control" type="text"
                             readonly />
                     </div>

                     <div class="col-md-6">
                         <label for="codigo_generacion" class="form-label">Código de generación</label>
                         <input id="codigo_generacion" name="codigo_generacion" class="form-control" type="text"
                             readonly />
                     </div>

                     <div class="col-md-6">
                         <label for="fecha_emision" class="form-label">Fecha Emisión</label>
                         <input id="fecha_emision" name="fecha_emision" class="form-control" type="date" readonly />
                     </div>

                     <div class="col-md-6">
                         <label for="tipo_invalidacion" class="form-label">Tipo de invalidación</label>
                         <select id="tipo_invalidacion" name="tipo_invalidacion" class="form-select" required>
                             <option value="">Seleccionar</option>
                             <option value="2">Rescindir de la operación realizada</option>
                         </select>
                     </div>

                     <div class="col-md-6">
                         <label for="motivo_anulacion" class="form-label">Motivo de anulación</label>
                         <textarea id="motivo_anulacion" name="motivo_anulacion" class="form-control" rows="2"
                             placeholder="Ej. Error en la descripción del producto" required></textarea>
                     </div>

                     <div class="col-md-6">
                         <label for="nombre_responsable" class="form-label">Nombre del responsable</label>
                         <input id="nombre_responsable" name="nombre_responsable" class="form-control" type="text"
                             placeholder="Ej. Juan Pérez" required />
                     </div>

                     <div class="col-md-6">
                         <label for="tipo_doc_responsable" class="form-label">Tipo de documento responsable</label>
                         <select id="tipo_doc_responsable" name="tipo_doc_responsable" class="form-select" required>
                             <option value="">Seleccionar</option>
                             <option value="13">DUI</option>
                             <option value="36">NIT</option>
                         </select>
                     </div>

                     <div class="col-md-6">
                         <label for="num_doc_responsable" class="form-label">N° documento responsable</label>
                         <input id="num_doc_responsable" name="num_doc_responsable" class="form-control" type="text"
                             placeholder="Ej. 01234567-8" required />
                     </div>

                     <div class="col-md-6">
                         <label for="nombre_solicita" class="form-label">Nombre de quien solicita</label>
                         <input id="nombre_solicita" name="nombre_solicita" class="form-control" type="text"
                             placeholder="Ej. María López" required />
                     </div>

                     <div class="col-md-6">
                         <label for="tipo_doc_solicita" class="form-label">Tipo de documento solicitante</label>
                         <select id="tipo_doc_solicita" name="tipo_doc_solicita" class="form-select" required>
                             <option value="">Seleccionar</option>
                             <option value="13">DUI</option>
                             <option value="36">NIT</option>
                         </select>
                     </div>

                     <div class="col-md-6">
                         <label for="num_doc_solicita" class="form-label">N° documento solicitante</label>
                         <input id="num_doc_solicita" name="num_doc_solicita" class="form-control" type="text"
                             placeholder="Ej. 87654321-0" required />
                     </div>

                     <div class="col-12 text-center mt-4">
                         <button type="submit" class="btn btn-primary me-2">Sí, anular</button>
                         <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal"
                             aria-label="Cerrar">Cancelar</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>

 <!-- Modal Spinner (debe estar fuera del modal principal) -->
 <div class="modal fade" id="spinnerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
     data-bs-keyboard="false">
     <div class="modal-dialog modal-dialog-centered">
         <div
             class="modal-content bg-light border-0 shadow-lg rounded-4 text-center p-4 d-flex flex-column align-items-center justify-content-center">
             <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                 <span class="visually-hidden">Cargando...</span>
             </div>
             <h5 class="text-dark fw-semibold text-center">Anulando DTE, por favor espere...</h5>
         </div>
     </div>
 </div>

 <!--/ Add New Credit Card Modal -->

 <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
 <script>
     $(document).ready(function() {
         $(document).on('click', '.btn-anular-json', function() {
             $('#anularJson').modal('show');

             $('#documento_id').val($(this).data('id'));
             $('#numero_control').val($(this).data('numero_control'));
             $('#codigo_generacion').val($(this).data('codigo_generacion'));
             $('#tipo_documento').val($(this).data('tipo_documento'));
             $('#fecha_emision').val($(this).data('fecha_emision'));
         });


         $('#anularDTEMH').on('submit', function(e) {
             e.preventDefault();

             let id = $('#documento_id').val();
             let motivo = $('#tipo_invalidacion').val();

             if (!motivo) {
                 Swal.fire('Selecciona un motivo de anulación', '', 'warning');
                 return;
             }

             let url = `/facturacion/anular-json/${id}`;
             let token = $('input[name="_token"]').val();

             let datosMotivo = {
                 tipo_invalidacion: motivo,
                 motivo_anulacion: $('#motivo_anulacion').val(),
                 nombre_responsable: $('#nombre_responsable').val(),
                 tipo_doc_responsable: $('#tipo_doc_responsable').val(),
                 num_doc_responsable: $('#num_doc_responsable').val(),
                 nombre_solicita: $('#nombre_solicita').val(),
                 tipo_doc_solicita: $('#tipo_doc_solicita').val(),
                 num_doc_solicita: $('#num_doc_solicita').val()
             };

             for (const campo in datosMotivo) {
                 if (!datosMotivo[campo]) {
                     Swal.fire('Completa todos los campos del formulario', '', 'warning');
                     return;
                 }
             }

             $('#anularDTEMH').find('button[type="submit"], button[type="reset"]').prop('disabled',
                 true);

             $('#spinnerModal').modal('show');

             $.ajax({
                 url: url,
                 method: 'POST',
                 data: {
                     _token: token,
                     ...datosMotivo
                 },
                 success: function(response) {
                     $('#spinnerModal').modal('hide');
                     $('#anularJson').modal('hide');
                     $('#anularDTEMH').find('button[type="submit"], button[type="reset"]')
                         .prop('disabled', false);

                     Toastify({
                         text: "Documento anulado correctamente.",
                         className: "success",
                         style: {
                             background: "linear-gradient(to right, #28a745, #218838)"
                         }
                     }).showToast();
                     $('#sysconta-datatable').DataTable().ajax.reload(null, false);
                 },
                 error: function(e) {
                     $('#spinnerModal').modal('hide');
                     $('#anularDTEMH').find('button[type="submit"], button[type="reset"]')
                         .prop('disabled', false);

                     let mensaje = e.responseJSON?.error || 'Error desconocido';
                     Swal.fire('Error al anular', mensaje, 'error');
                 }
             });
         });

     });
 </script>
