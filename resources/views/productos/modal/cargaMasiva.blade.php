{{-- Modal --}}
<div class="modal fade" id="cargaMasivaProductos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <div class="text-center mb-4">
                    <h4 class="mb-2">Agregar carga masiva de productos</h4>
                    <div class="alert alert-warning fade show position-relative" role="alert">
                        <strong>¡Importante!</strong> A continuación podrás agregar carga masiva de productos y
                        descargar su respectiva plantilla.
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                            data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>

                {{-- Descargar plantilla --}}
                <div class="col-12 mb-4">
                    <label class="form-label w-100">Descargar plantilla</label>
                    <div class="input-group input-group-merge">
                        <a href="{{ route('productos.descargarPlantilla') }}" class="btn btn-primary w-100">
                            <i class="bx bx-down-arrow-alt"></i> Descargar plantilla
                        </a>
                    </div>
                </div>

                {{-- Seleccionar archivo --}}
                <form id="seleccionarPlantilla" method="POST"
                    action="{{ route('productos.enviarCargaMasivadeProductos') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12 mb-3">
                        <label class="form-label w-100">Seleccionar archivo de plantilla</label>
                        <div class="input-group input-group-merge">
                            <label for="archivoPlantilla" class="btn btn-white text-dark mt-mobile w-100">
                                <i class="bx bx-upload"></i> Seleccionar archivo
                            </label>
                            <input type="file" id="archivoPlantilla" name="file" class="d-none">
                            <span id="nombreArchivo" class="ms-2"></span>
                        </div>
                    </div>

                    {{-- Botón Procesar, oculto inicialmente --}}
                    <div id="btnProcesarWrapper" class="col-12 mt-3 d-none d-flex justify-content-center">
                        <button type="submit" class="btn btn-dark w-90">
                            <i class="bx bx-right-arrow-alt"></i> Procesar
                        </button>
                        <button type="reset" class="btn btn-danger w-90" id="cerrarModalBtn">
                            <i class="bx bx-right-arrow-alt"></i> Cerrar
                        </button>
                    </div>
                </form>

                <div id="spinner" class="d-none text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p>Cargando, por favor espere...</p>
                </div>


                {{-- Tabla de productos procesados --}}
                <div id="tablaProductosProcesados" class="col-12 mt-4 d-none">
                    <h5 class="mb-3 text-center">Productos Procesados</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-bordered table-sm">
                            <thead class="table-white">
                                <tr>
                                    <th>Codigo</th>
                                    <th>Nombre</th>
                                    <th>Precio-compra</th>
                                    <th>Precio-venta</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Descripcion</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<!--/ Modal carga masiva -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputArchivo = document.getElementById('archivoPlantilla');
        const btnWrapper = document.getElementById('btnProcesarWrapper');
        const tablaProcesados = document.getElementById('tablaProductosProcesados');
        const tbody = tablaProcesados.querySelector('tbody');

        inputArchivo.addEventListener('change', function() {
            const fileNameSpan = document.getElementById('nombreArchivo');
            if (this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
                btnWrapper.classList.remove('d-none');
            } else {
                fileNameSpan.textContent = '';
                btnWrapper.classList.add('d-none');
                tablaProcesados.classList.add('d-none');
            }
        });

        document.getElementById('seleccionarPlantilla').addEventListener('submit', function(e) {
            e.preventDefault();
            /* Mostrar spinner */
            document.getElementById('spinner').classList.remove('d-none');

            const form = this;
            const formData = new FormData(form);

            fetch("{{ route('productos.enviarCargaMasivadeProductos') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    /* Ocultar spinner */
                    document.getElementById('spinner').classList.add('d-none');

                    if (data.success) {
                        /* Mostramos el mensaje de exito con Toastify */
                        Toastify({
                            text: "Productos importados correctamente.",
                            duration: 4000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(to right, #28a745, #218838)",
                        }).showToast();

                        /* Limpiar tabla */
                        tbody.innerHTML = '';

                        /* Agregar filas */
                        data.productos.forEach(p => {
                            tbody.innerHTML += `
                    <tr>
                        <td>${p.codigo}</td>
                        <td>${p.nombre}</td>
                        <td>${p.precio_compra}</td>
                        <td>${p.precio_venta}</td>
                        <td>${p.categoria}</td>
                        <td>${p.stock}</td>
                        <td>${p.descripcion ?? ''}</td>
                    </tr>
                    `;
                        });

                        /* Mostramos la tabla */
                        tablaProcesados.classList.remove('d-none');
                    } else {
                        /* Manejo de errores */
                        console.log("Errores encontrados: ", data.errores_url);

                        Toastify({
                            text: data.message,
                            duration: 5000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(to right, #ffc107, #ff9800)",
                        }).showToast();

                        /* Descargar archivo de errores si existe */
                        if (data.errores_url) {
                            const linkErrores = document.createElement('a');
                            linkErrores.href = data.errores_url;
                            linkErrores.setAttribute('download', data.errores_url.split('/').pop());
                            document.body.appendChild(linkErrores);
                            linkErrores.click();
                            document.body.removeChild(linkErrores);
                        }
                    }

                    /* Descargar productos validados si existe la URL */
                    if (data.productos_url) {
                        const linkProductos = document.createElement('a');
                        linkProductos.href = data.productos_url;
                        linkProductos.download = data.productos_url.split('/').pop();
                        document.body.appendChild(linkProductos);
                        linkProductos.click();
                        document.body.removeChild(linkProductos);
                    }
                })
                .catch(error => {
                    console.error('Error al procesar:', error);

                    /* Ocultar spinner en caso de error */
                    document.getElementById('spinner').classList.add('d-none');

                    Toastify({
                        text: "Ocurrió un error al importar los productos.",
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #dc3545, #c82333)",
                    }).showToast();
                });
        });


        document.getElementById('cerrarModalBtn').addEventListener('click', function() {
            $('#cargaMasivaProductos').modal('hide');
            document.getElementById('seleccionarPlantilla').reset();
            document.getElementById('nombreArchivo').textContent = '';
            document.getElementById('btnProcesarWrapper').classList.add('d-none');
            document.getElementById('tablaProductosProcesados').classList.add(
            'd-none');
            $('#sysconta-datatable').DataTable().ajax.reload();
        });

    });
</script>
