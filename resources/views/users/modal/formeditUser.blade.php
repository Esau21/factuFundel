 {{-- modal --}}
 <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Close"></button>
                 <div class="text-center mb-6">
                     <h4 class="mb-2">Editar usuario</h4>
                     <div class="alert alert-danger fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>
                 <form id="editFormUser" class="row g-6" onsubmit="return false">
                     @csrf
                     <input type="hidden" name="user_id" id="user_id">
                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="categoria_nombre">Nombre</label>
                         <div class="input-group input-group-merge">
                             <input id="name" name="name" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="categoria_nombre">Email</label>
                         <div class="input-group input-group-merge">
                             <input id="email" name="email" class="form-control" type="email"
                                 placeholder="ejemplo123@gmail.com" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="categoria_nombre">Clave</label>
                         <div class="input-group input-group-merge">
                             <input id="password" name="password" class="form-control" type="password"
                                 placeholder="********" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>


                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="categoria_nombre">Perfil</label>
                         <div class="input-group input-group-merge">
                             <select name="profile" id="profile" class="select2 w-100 form-control"
                                 data-dropdown-parent="#editUser">
                                 <option value="">Elegir</option>
                                 @foreach ($roles as $r)
                                     <option value="{{ $r->name }}">{{ $r->name }}</option>
                                 @endforeach
                             </select>
                         </div>
                     </div>

                     <div class="col-12 form-control-validation">
                        <label class="form-label w-100" for="empresa_id">Empresa</label>
                        <div class="input-group input-group-merge">
                            <select name="empresa_id" id="empresa_id" class="select2 w-100 form-control"
                                data-dropdown-parent="#editUser">
                                <option value="">Elegir</option>
                                @foreach ($empresas as $em)
                                    <option value="{{ $em->id }}">{{ $em->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="status">Estado</label>
                         <div class="input-group input-group-merge">
                             <select name="status" id="status" class="form-control">
                                 <option value="">Elegir</option>
                                 <option value="Active">ACTIVO</option>
                                 <option value="Locked">BLOQUEADO</option>
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
     $(document).on('click', '.btn-editar-user', function() {
         let id = $(this).data('id');
         let name = $(this).data('name');
         let email = $(this).data('email');
         let profile = $(this).data('profile');
         let status = $(this).data('status');
         let empresa = $(this).data('empresa_id');

         $('#editUser #user_id').val(id);
         $('#editUser #name').val(name);
         $('#editUser #email').val(email);
         $('#editUser #profile').val(profile).trigger('change');
         $('#editUser #status').val(status);
         $('#editUser #empresa_id').val(empresa).trigger('change');
     });

     $('#editFormUser').on('submit', function(e) {
         e.preventDefault();

         let id = $('#user_id').val();
         let url = `/update/user/${id}`;
         let formData = $(this).serialize();

         $.ajax({
             url: url,
             method: 'POST',
             data: formData,
             headers: {
                 'X-CSRF-TOKEN': $('input[name="_token"]').val()
             },
             success: function(response) {
                 $('#editUser').modal('hide');
                 $('#editFormUser')[0].reset();

                 var table = $('#sysconta-datatable').DataTable();
                 table.ajax.reload(null, false);

                 Toastify({
                     text: "Usuario actualizado correctamente.",
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
                     $('#editUser').modal('hide');
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
                         title: 'Algo salió mal al actualizar la categoría.',
                         icon: 'error'
                     });
                 }
             }
         });
     });
 </script>
