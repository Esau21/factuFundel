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
                        <a href="#" class="btn btn-primary w-100">
                            <i class="bx bx-down-arrow-alt"></i> Descargar plantilla
                        </a>
                    </div>
                </div>

                {{-- Seleccionar archivo --}}
                <form id="seleccionarPlantilla" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-12 mb-3">
                        <label class="form-label w-100">Seleccionar archivo de plantilla</label>
                        <div class="input-group input-group-merge">
                            <label for="archivoPlantilla" class="btn btn-white text-dark mt-mobile w-100">
                                <i class="bx bx-upload"></i> Seleccionar archivo
                            </label>
                            <input type="file" id="archivoPlantilla" name="plantilla" class="d-none">
                        </div>
                    </div>

                    {{-- Botón Procesar, oculto inicialmente --}}
                    <div id="btnProcesarWrapper" class="col-12 mt-3 d-none d-flex justify-content-center">
                        <button type="submit" class="btn btn-dark w-90">
                            <i class="bx bx-right-arrow-alt"></i> Procesar
                        </button>
                    </div>
                </form>


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
                                {{-- Ejemplo de fila --}}
                                <tr>
                                    <td>0001</td>
                                    <td>Producto A</td>
                                    <td>PA-001</td>
                                    <td>Categoria X</td>
                                    <td>$10.00</td>
                                    <td>100</td>
                                    <td>100</td>
                                </tr>
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

        inputArchivo.addEventListener('change', function() {
            if (this.files.length > 0) {
                btnWrapper.classList.remove('d-none');
            } else {
                btnWrapper.classList.add('d-none');
                tablaProcesados.classList.add('d-none');
            }
        });

        // Mostrar tabla al enviar el formulario (simulado)
        document.getElementById('seleccionarPlantilla').addEventListener('submit', function(e) {
            e.preventDefault(); // Quita esta línea si quieres procesar de verdad en el backend
            tablaProcesados.classList.remove('d-none');
        });
    });
</script>
