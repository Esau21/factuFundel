 {{-- modal --}}
 <div class="modal fade" id="editCuentaBancaria" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered1 modal-xl modal-simple modal-add-new-cc">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Close"></button>
                 <div class="text-center mb-6">
                     <h4 class="mb-2">Editar cuenta bancaria</h4>
                     <div class="alert alert-warning fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos que se te solicitan a continuación.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>
                 <form id="editNewccCuentasBancarias" class="row g-6" onsubmit="return false">
                     @csrf
                     <input type="hidden" name="cuenta_id" id="cuenta_id">
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
                         <label class="form-label w-100" for="tipo_cuenta">Selecciona el Cliente</label>
                         <div class="input-group input-group-merge">
                             <select name="cliente_id" id="cliente_id" class="form-control select2"
                                 data-dropdown-parent="#addCuentaBancaria">
                                 <option value="">Elegir</option>
                                 @foreach ($clientes as $c)
                                     <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                 @endforeach
                             </select>
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
     $(document).on('click', '.btn-editar-cuentabancaria', function() {
         let id = $(this).data('id');
         let numero_cuenta = $(this).data('numero_cuenta');
         let tipo_cuenta = $(this).data('tipo_cuenta');
         let cliente_id = $(this).data('cliente_id');
         let estado = $(this).data('estado');

         $('#editCuentaBancaria #cuenta_id').val(id);
         $('#editCuentaBancaria #numero_cuenta').val(numero_cuenta);
         $('#editCuentaBancaria #tipo_cuenta').val(tipo_cuenta);
         $('#editCuentaBancaria #cliente_id').val(cliente_id);
         $('#editCuentaBancaria #estado').val(estado);
     });

     $('#editNewccCuentasBancarias').on('submit', function(e) {
         e.preventDefault();

         let cuentaId = $('#cuenta_id').val();
         let url = `/cuenta/update/bancaria/${cuentaId}`;
         let formData = $(this).serialize();

         $.ajax({
             url: url,
             method: 'POST',
             data: formData,
             headers: {
                 'X-CSRF-TOKEN': $('input[name="_token"]').val()
             },
             success: function(response) {
                 $('#editCuentaBancaria').modal('hide');
                 $('#editNewccCuentasBancarias')[0].reset();

                 var table = $('#sysconta-datatable').DataTable();
                 table.ajax.reload(null, false);

                 Toastify({
                     text: "Cuenta actualizada correctamente.",
                     className: "info",
                     style: {
                         background: "linear-gradient(to right, #3b3f5c, #3b3f5c)"
                     }
                 }).showToast();
             },
             error: function(e) {
                 if (e.status === 422) {
                     let errors = e.responseJSON.errors;
                     let errorMessage = '';
                     $.each(errors, function(key, value) {
                         errorMessage += value.join('<br>');
                     });
                     $('#editCuentaBancaria').modal('hide');
                     Toastify({
                         text: `${errorMessage}`,
                         className: "error",
                         gravity: "top",
                         position: "right",
                         duration: 5000,
                         style: {
                             background: "linear-gradient(to right, #F7374F, #F7374F)",
                             whiteSpace: "pre-line"
                         }
                     }).showToast();

                 } else {
                     Swal.fire({
                         title: 'Algo salió mal al actualizar el banco.',
                         icon: 'error'
                     });
                 }
             }
         });
     });
 </script>
