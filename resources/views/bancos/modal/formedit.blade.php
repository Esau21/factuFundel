 {{-- modal --}}
 <div class="modal fade" id="editBanco" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Close"></button>
                 <div class="text-center mb-6">
                     <h4 class="mb-2">Editar banco</h4>
                     <div class="alert alert-danger fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>
                 <form id="editbancoForm" class="row g-6" onsubmit="return false">
                     @csrf
                     <input type="hidden" name="banco_id" id="banco_id">

                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="nombre">Nombre banco</label>
                         <div class="input-group input-group-merge">
                             <input id="nombre" name="nombre" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="codigo">Codigo</label>
                         <div class="input-group input-group-merge">
                             <input id="codigo" name="codigo" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="estado">Estado del banco</label>
                         <div class="input-group input-group-merge">
                             <select name="estado" id="estado" class="form-control">
                                 <option value="">Elegir</option>
                                 <option value="1">Active</option>
                                 <option value="0">locked</option>
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
     $(document).on('click', '.btn-editar-banco', function() {
         let id = $(this).data('id');
         let nombre = $(this).data('nombre');
         let codigo = $(this).data('codigo');
         let estado = $(this).data('estado');

         $('#editBanco #banco_id').val(id);
         $('#editBanco #nombre').val(nombre);
         $('#editBanco #codigo').val(codigo);
         $('#editBanco #estado').val(estado);
     });

     $('#editbancoForm').on('submit', function(e) {
         e.preventDefault();

         let id = $('#banco_id').val();
         let url = `/update/banco/${id}`;
         let formData = $(this).serialize();

         $.ajax({
             url: url,
             method: 'POST',
             data: formData,
             headers: {
                 'X-CSRF-TOKEN': $('input[name="_token"]').val()
             },
             success: function(response) {
                 $('#editBanco').modal('hide');
                 $('#editbancoForm')[0].reset();

                 var table = $('#sysconta-datatable').DataTable();
                 table.ajax.reload(null, false);

                 Toastify({
                     text: "Banco actualizado correctamente.",
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
                     $('#editBanco').modal('hide');
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
