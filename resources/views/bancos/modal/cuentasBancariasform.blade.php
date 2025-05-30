 {{-- modal --}}
 <div class="modal fade" id="addCuentaBancaria" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered1 modal-xl modal-simple modal-add-new-cc">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Close"></button>
                 <div class="text-center mb-6">
                     <h4 class="mb-2">Agregar nueva cuenta bancaria</h4>
                     <div class="alert alert-dark fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos que se te solicitan a continuación.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>
                 <form id="addNewCCFormCuentas" class="row g-6" onsubmit="return false">
                     @csrf
                     <div class="col-6 form-control-validation">
                         <label class="form-label w-100" for="numero_cuenta">Numero de cuenta</label>
                         <div class="input-group input-group-merge">
                             <input id="numero_cuenta" name="numero_cuenta" class="form-control" type="text"
                                 placeholder="XXXXXX-XXXXXX" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-6 form-control-validation">
                         <label class="form-label w-100" for="tipo_cuenta">Tipo de cuenta</label>
                         <div class="input-group input-group-merge">
                             <select name="tipo_cuenta" id="tipo_cuenta" class="form-control">
                                 <option value="">Elegir</option>
                                 <option value="ahorro">AHORRO</option>
                                 <option value="corriente">CORRIENTE</option>
                                 <option value="credito">CREDITO</option>
                             </select>
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>


                     <div class="col-6 form-control-validation">
                         <label class="form-label w-100" for="titular">Titular</label>
                         <div class="input-group input-group-merge">
                             <input id="titular" name="titular" class="form-control" type="text"
                                 placeholder="Ingresa el nombre del titular" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-6 form-control-validation">
                         <label class="form-label w-100" for="tipo_cuenta">Estado</label>
                         <div class="input-group input-group-merge">
                             <select name="estado" id="estado" class="form-control">
                                 <option value="">Elegir</option>
                                 <option value="1">ACTIVO</option>
                                 <option value="0">BLOQUEADO</option>
                             </select>
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
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
         $("#addNewCCFormCuentas").on('submit', function(e) {
             e.preventDefault();
             let bancoId = {{ $banco->id }};
             let url = '/cuenta/bancaria/store/' + bancoId;
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
                     $('#addCuentaBancaria').modal('hide');
                     $('#addNewCCFormCuentas')[0].reset();

                     var table = $('#sysconta-datatable').DataTable();
                     table.ajax.reload(null, false);

                     Toastify({
                         text: "El banco se agregó correctamente.",
                         className: "info",
                         style: {
                             background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                         }
                     }).showToast();
                 },
                 error: function(e) {
                     $('#addCuentaBancaria').modal('hide');
                     $('#addNewCCFormCuentas')[0].reset();
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
