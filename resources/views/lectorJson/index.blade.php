@extends('layouts.sneatTheme.base')

@section('content')
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }

        /* Solo unos detalles para la zona de dropzone */
        .dropzone-wrapper {
            cursor: pointer;
            user-select: none;
            border: 2px dashed #20c997;
            /* bootstrap "success" color */
            border-radius: 0.5rem;
            padding: 3rem 2rem;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .dropzone-wrapper.dragover {
            background-color: #d1e7dd !important;
            /* bg success light */
            border-color: #157347 !important;
            /* bootstrap dark success */
        }

        #file-upload {
            display: none;
        }

        #clean_data {
            cursor: pointer;
        }
    </style>

    <div class="container upload-section mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow rounded-4">
                    <div class="card-header text-center bg-white border-0">
                        <h2 class="fw-bold">
                            Cargar archivos <span class="text-success">DTE</span>
                            <ion-icon name="arrow-forward-outline"></ion-icon>
                            <span class="text-success">Excel</span>
                        </h2>
                        <p class="text-muted">Carga múltiples archivos JSON arrastrando o seleccionando archivos.</p>
                    </div>
                    <div class="card-body">
                        <form id="upload-form" method="POST" action="{{ route('lector.uploadFile') }}"
                            enctype="multipart/form-data" class="text-center">
                            @csrf
                            @method('POST')

                            <label id="dropzone" for="file-upload" class="dropzone-wrapper d-block mx-auto">
                                <ion-icon name="cloud-upload-outline" style="font-size: 3rem; color: #20c997;"></ion-icon>
                                <p class="fs-5 fw-semibold mb-0">Arrastra tus archivos JSON aquí o haz clic para
                                    seleccionarlos</p>
                                <input id="file-upload" type="file" name="file[]" multiple>
                            </label>

                            <button type="submit" class="btn bg-label-success btn-lg mt-4 w-90" id="btn-file" disabled>
                                Convertir a Excel
                            </button>
                        </form>

                        <div id="loading" class="mt-3 text-center" style="display: none;">
                            <div class="spinner-border text-dark" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 text-start">
                        <h5 id="text-h5-json" class="fw-bold mb-3" style="display: none;">Archivos JSON cargados:</h5>
                        <div id="file-list" class="mb-3"></div>
                        <button id="clean_data" class="btn bg-label-danger" style="display: none;">
                            <i class="bx bx-recycle"></i> Limpiar selección
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const dropzone = document.getElementById("dropzone");
        const subirArchivo = document.getElementById("file-upload");
        const listarArchivo = document.getElementById("file-list");
        const texttoH5 = document.getElementById("text-h5-json");
        const Clear = document.getElementById("clean_data");
        const btnFile = document.getElementById("btn-file");

        function mostrarArchivos(files) {
            listarArchivo.innerHTML = '';
            [...files].forEach(file => {
                const item = document.createElement('div');
                item.classList.add('d-flex', 'align-items-center', 'mb-2', 'border', 'rounded', 'p-2',
                    'bg-white', 'shadow-sm');
                item.innerHTML = `
                <div class="flex-grow-1">
                    <div class="fw-bold">${file.name}</div>
                    <div class="text-muted small">${formatFileSize(file.size)}</div>
                </div>
            `;
                listarArchivo.appendChild(item);
            });

            texttoH5.innerHTML =
                `Archivos JSON cargados: <span class="badge bg-success">${files.length}</span>`;
            texttoH5.style.display = 'block';
            Clear.style.display = 'inline-block';
            btnFile.disabled = false;
        }

        function formatFileSize(size) {
            if (size === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(size) / Math.log(k));
            return (size / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i];
        }

        subirArchivo.addEventListener('change', function() {
            if (this.files.length) {
                mostrarArchivos(this.files);
            } else {
                listarArchivo.innerHTML = '';
                texttoH5.style.display = 'none';
                Clear.style.display = 'none';
                btnFile.disabled = true;
            }
        });

        Clear.addEventListener('click', () => {
            listarArchivo.innerHTML = '';
            texttoH5.style.display = 'none';
            Clear.style.display = 'none';
            subirArchivo.value = '';
            btnFile.disabled = true;
        });

        dropzone.addEventListener('dragover', e => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('dragover');

            const files = e.dataTransfer.files;
            subirArchivo.files = files;
            mostrarArchivos(files);
        });

        btnFile.disabled = true;
    });
</script>

<script>
    $(document).ready(function() {
        $('#upload-form').on('submit', function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            $('#loading').show();

            $.ajax({
                url: '{{ route('lector.uploadFile') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob, status, xhr) {
                    let link = document.createElement('a');
                    let url = window.URL.createObjectURL(blob);
                    link.href = url;
                    link.download = 'dte-transformado-by-moranZsoft.xlsx';
                    document.body.append(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(url);

                    Toastify({
                        text: "✅ Se ha descargado el excel correctamente",
                        duration: 2000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(to right, #3b3f5c, #3b3f5c)",
                        stopOnFocus: true,
                        close: true,
                        style: {
                            borderRadius: "8px",
                            boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
                            fontWeight: "600",
                            fontSize: "16px",
                        }
                    }).showToast();


                    $('#loading').hide();
                    $('#file-list').empty();
                    $('#text-h5-json').hide();
                    $('#clean_data').hide();
                    $('#file-upload').val('');
                    $('#btn-file').prop('disabled', true);
                },
                error: function(jqXHR) {
                    setTimeout(() => {
                        if (jqXHR.status == 415) {
                            Toastify({
                                text: "Error, uno o más archivos cargados no son JSON",
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(to right, #FF0000, #FF0000)",
                            }).showToast();
                        } else if (jqXHR.status == 400) {
                            Toastify({
                                text: "solo puedes procesar 500 dte al dia",
                                duration: 25000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(to right, #667BC6, #667BC6)",
                            }).showToast();
                        } else {
                            Toastify({
                                text: "Error desconocido, contacte al administrador",
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(to right, #FF0000, #FF0000)",
                            }).showToast();
                        }

                        $('#file-list').empty();
                        $('#text-h5-json').hide();
                        $('#clean_data').hide();
                        $('#file-upload').val('');
                        $('#btn-file').prop('disabled', true);
                        $('#loading').hide();
                    }, 1000);
                }
            });
        });
    });
</script>
