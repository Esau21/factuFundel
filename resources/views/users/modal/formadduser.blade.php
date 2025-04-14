 {{-- modal --}}
 <div class="modal fade" id="addUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Agregar nuevo usuario</h4>
                    <div class="alert alert-danger fade show position-relative" role="alert">
                        <strong>Importante!</strong> Completa todos los campos.
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                            data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <form id="addFormUser" class="row g-6" onsubmit="return false">
                    @csrf
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
                                data-dropdown-parent="#addUser">
                                <option value="">Elegir</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
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
    $(document).ready(function() {
        $("#addFormUser").on('submit', function(e) {
            e.preventDefault();
            let url = "{{ route('usuarios.storeUser') }}";
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
                    $('#addUser').modal('hide');
                    $('#addFormUser')[0].reset();

                    var table = $('#sysconta-datatable').DataTable();
                    table.ajax.reload(null, false);

                    Toastify({
                        text: "El usuario se agregó correctamente.",
                        className: "info",
                        style: {
                            background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                        }
                    }).showToast();
                },
                error: function(e) {
                    $('#addUser').modal('hide');
                    $('#addFormUser')[0].reset();
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
