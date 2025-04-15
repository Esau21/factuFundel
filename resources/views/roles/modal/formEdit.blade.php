 {{-- modal --}}
 <div class="modal fade" id="editRole" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Close"></button>
                 <div class="text-center mb-6">
                     <h4 class="mb-2">Editar rol</h4>
                     <div class="alert alert-danger fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>
                 <form id="editRoleForm" class="row g-6" onsubmit="return false">
                     @csrf
                     <input type="hidden" name="role_id" id="role_id">
                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="namerole">Nombre rol</label>
                         <div class="input-group input-group-merge">
                             <input id="name" name="name" class="form-control" type="text"
                                 placeholder="ejemplo" />
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
     $(document).on('click', '.btn-editar-role', function() {
         let id = $(this).data('id');
         let name = $(this).data('name');

         $('#editRole #role_id').val(id);
         $('#editRole #name').val(name);
     });

     $('#editRoleForm').on('submit', function(e) {
         e.preventDefault();

         let id = $('#role_id').val();
         let url = `/update/role/${id}`;
         let formData = $(this).serialize();

         $.ajax({
             url: url,
             method: 'POST',
             data: formData,
             headers: {
                 'X-CSRF-TOKEN': $('input[name="_token"]').val()
             },
             success: function(response) {
                 $('#editRole').modal('hide');
                 $('#editRoleForm')[0].reset();

                 var table = $('#sysconta-datatable').DataTable();
                 table.ajax.reload(null, false);

                 Toastify({
                     text: "Rol actualizado correctamente.",
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
                     $('#editRole').modal('hide');
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
                     let msg = e.responseJSON?.error || 'Ocurrió un error inesperado';
                     $('#editRole').modal('hide');
                     Toastify({
                         text: msg,
                         className: "error",
                         gravity: "top",
                         position: "right",
                         duration: 5000,
                         style: {
                             background: "linear-gradient(to right, #F7374F, #F7374F)",
                             whiteSpace: "pre-line"
                         }
                     }).showToast();
                 }
             }
         });
     });
 </script>
