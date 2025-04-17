@extends('layouts.sneatTheme.base')
@section('title', 'Nuevo producto')
@section('content')


    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom mb-3">
                        <h5 class="card-title mb-0">Agregar nuevo producto al sistema.</h5>
                    </div>
                    <div class="card-body">
                        <form id="addFormProduct" class="row g-6" onsubmit="return false" enctype="multipart/form-data">
                            @csrf
                            <!-- Imagen -->
                            <!-- Imagen -->
                            <div class="col-12 form-control-validation">
                                <label class="form-label w-100" for="imagen">Imagen</label>
                                <div class="input-group input-group-merge">
                                    <input id="imagen" name="imagen" class="form-control" type="file"
                                        onchange="previewImage(event)" />
                                    <span class="input-group-text cursor-pointer">
                                        <span class="card-type me-n2"></span>
                                    </span>
                                </div>
                                <div id="imagePreviewContainer" style="margin-top: 10px;">
                                    <img id="imagePreview" src="" alt="Vista previa de la imagen"
                                        style="max-width: 100%; max-height: 300px; display: none;" />
                                </div>
                            </div>


                            <!-- Codigo producto -->
                            <div class="col-4 form-control-validation">
                                <label class="form-label w-100" for="codigo">Codigo producto</label>
                                <div class="input-group input-group-merge">
                                    <input id="codigo" name="codigo" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Nombre producto -->
                            <div class="col-4 form-control-validation">
                                <label class="form-label w-100" for="nombre">Nombre producto</label>
                                <div class="input-group input-group-merge">
                                    <input id="nombre" name="nombre" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Categoria -->
                            <div class="col-12 form-control-validation">
                                <label class="form-label w-100" for="categoria_id">Categoria</label>
                                <div class="input-group input-group-merge">
                                    <select name="categoria_id" id="categoria_id" class="select2 w-100 form-control">
                                        <option value="">Elegir</option>
                                        @foreach ($categorias as $c)
                                            <option value="{{ $c->id }}">{{ $c->categoria_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Descripcion -->
                            <div class="col-12 form-control-validation">
                                <label class="form-label w-100" for="descripcion">Descripcion</label>
                                <div class="input-group input-group-merge">
                                    <textarea name="descripcion" id="descripcion" cols="30" rows="1" class="form-control"
                                        placeholder="Ingresa tu descripcion"></textarea>
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Precio compra -->
                            <div class="col-3 form-control-validation">
                                <label class="form-label w-100" for="precio_compra">Precio compra</label>
                                <div class="input-group input-group-merge">
                                    <input id="precio_compra" name="precio_compra" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Precio venta -->
                            <div class="col-3 form-control-validation">
                                <label class="form-label w-100" for="precio_venta">Precio venta</label>
                                <div class="input-group input-group-merge">
                                    <input id="precio_venta" name="precio_venta" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="col-3 form-control-validation">
                                <label class="form-label w-100" for="stock">Stock</label>
                                <div class="input-group input-group-merge">
                                    <input id="stock" name="stock" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Stock minimo -->
                            <div class="col-3 form-control-validation">
                                <label class="form-label w-100" for="stock_minimo">Stock minimo</label>
                                <div class="input-group input-group-merge">
                                    <input id="stock_minimo" name="stock_minimo" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Unidad medida -->
                            <div class="col-3 form-control-validation">
                                <label class="form-label w-100" for="unidad_medida">Unidad medida</label>
                                <div class="input-group input-group-merge">
                                    <input id="unidad_medida" name="unidad_medida" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Marca -->
                            <div class="col-3 form-control-validation">
                                <label class="form-label w-100" for="marca">Marca</label>
                                <div class="input-group input-group-merge">
                                    <input id="marca" name="marca" class="form-control" type="text"
                                        placeholder="ejemplo" />
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-6 form-control-validation">
                                <label class="form-label w-100" for="estado">Estado</label>
                                <div class="input-group input-group-merge">
                                    <select name="estado" id="estado" class="form-control">
                                        <option value="">Elegir</option>
                                        <option value="activo">activo</option>
                                        <option value="deshabilitado">deshabilitado</option>
                                    </select>
                                    <span class="input-group-text cursor-pointer"><span
                                            class="card-type me-n2"></span></span>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">Guardar</button>
                                <a href="{{ route('productos.index') }}" class="btn btn-label-secondary btn-reset">Regresar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block'; 
        }

        if (file) {
            reader.readAsDataURL(file); 
        }
    }
</script>

<script>
    $(document).ready(function() {
        $("#addFormProduct").on('submit', function(e) {
            e.preventDefault();
            let url = "{{ route('productos.storeProduct') }}";
            const form = this;
            const formData = new FormData(form);
            var btnSubmit = $(this);
            btnSubmit.prop('disabled', true);

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                },
                success: function() {
                    Toastify({
                        text: "El producto se agregó correctamente.",
                        className: "info",
                        style: {
                            background: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                        }
                    }).showToast();
                    setTimeout(() => {
                        window.location.href = "{{ route('productos.index') }}";
                    }, 2000);
                },
                error: function(e) {
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
                        setTimeout(() => {
                            window.location.href = "{{ route('productos.index') }}";
                        }, 2000);
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
