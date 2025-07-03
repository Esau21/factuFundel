 {{-- modal --}}
 <div class="modal fade" id="addCaja" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-simple modal-add-new-cc modal-lg">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Close"></button>
                 <div class="text-center mb-6">
                     <h4 class="mb-2">Aperturar nueva caja</h4>
                     <div class="alert alert-danger fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>
                 <form id="addNewCCFormCaja" class="row g-6" onsubmit="return false">
                     @csrf
                     <div class="row">
                         <div class="col-sm-6 form-control-validation">
                             <label class="form-label w-100" for="fecha_apertura">Fecha de apertura</label>
                             <div class="input-group input-group-merge">
                                 <input id="fecha_apertura" name="fecha_apertura" class="form-control" type="date" />
                                 <span class="input-group-text cursor-pointer"><span
                                         class="card-type me-n2"></span></span>
                             </div>
                         </div>

                         <div class="col-sm-6 form-control-validation">
                             <label class="form-label w-100" for="monto_inicial">Monto Inicial</label>
                             <div class="input-group input-group-merge">
                                 <input type="text" name="monto_inicial" id="monto_inicial" class="form-control"
                                     placeholder="monto inicial">
                                 <span class="input-group-text cursor-pointer"><span
                                         class="card-type me-n2"></span></span>
                             </div>
                         </div>
                     </div>

                     <div class="col-12 text-center">
                         <button type="submit" class="btn btn-primary me-sm-3 me-1">Guardar</button>
                         <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                             aria-label="Close">Cancelar</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>
 <!--/ Add New Credit Card Modal -->

 <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

 <script>
     $(document).ready(function() {
         $("#addNewCCFormCaja").on('submit', function(e) {
             e.preventDefault();
             let url = "{{ route('cajas.storeCaja') }}";
             var btnSubmit = $(this);
             btnSubmit.prop('disabled', true);
             $.ajax({
                 url: url,
                 method: 'POST',
                 data: $(this).serialize(),
                 headers: {
                     'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                 },
                 success: function() {
                     $('#addCaja').modal('hide');
                     $('#addNewCCFormCaja')[0].reset();

                     var table = $('#sysconta-datatable').DataTable();
                     table.ajax.reload(null, false);

                     Toastify({
                         text: "La caja se aperturo correctamente.",
                         className: "info",
                         style: {
                             background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                         }
                     }).showToast();
                 },
                 error: function(e) {
                     $('#addCaja').modal('hide');
                     $('#addNewCCFormCaja')[0].reset();
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
