@extends('layouts.sneatTheme.base')

@section('title', 'Lector JSON')

@section('content')
    {{-- DROPZONE CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />

    <style>
        .dropzone {
            border: 2px dashed #03C988;
            background: #f0fdf4;
            border-radius: 10px;
            padding: 50px;
            text-align: center;
            color: #4b5563;
            transition: background 0.3s;
        }

        .dropzone:hover {
            background: #e7fdf1;
        }

        .dropzone .dz-message {
            font-size: 18px;
            color: #10b981;
        }

        .file-item {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 10px;
        }

        .file-item img {
            width: 40px;
            margin-right: 15px;
        }

        .file-name {
            font-weight: bold;
            color: #374151;
        }

        .btn {
            background-color: #03C988;
            color: white;
            border: none;
        }

        .btn:hover {
            background-color: #00b09b;
        }
    </style>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-white text-center">
                        <h2 class="text-dark font-weight-bold mb-2">
                            Conversor <span class="text-warning">JSON</span>
                            <ion-icon name="arrow-forward-outline"></ion-icon>
                            <span class="text-success">Excel</span>
                        </h2>
                        <p class="text-muted">Arrastra o selecciona múltiples archivos JSON</p>
                    </div>

                    <div class="card-body">
                        <form action="#" method="POST" class="dropzone" id="jsonDropzone"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="dz-message">
                                <ion-icon name="cloud-upload-outline" size="large"></ion-icon><br>
                                Arrastra tus archivos JSON aquí o haz clic para seleccionarlos
                            </div>
                        </form>

                        <div id="loading" class="text-center mt-3" style="display: none;">
                            <div class="spinner-border text-success" role="status"></div>
                        </div>

                        <div id="file-list" class="mt-4"></div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button id="convertir"
                                class="btn bg-label-success text-success border d-inline-flex align-items-center px-3 py-2"
                                disabled>
                                <i class="bx bx-file me-2" style="font-size: 18px;"></i> Convertir a Excel
                            </button>

                            <button id="limpiar"
                                class="btn bg-label-danger text-danger border d-inline-flex align-items-center px-3 py-2"
                                style="display: none;">
                                <i class="bx bxs-trash me-2" style="font-size: 18px;"></i> Limpiar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DROPZONE + TOASTIFY --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        Dropzone.autoDiscover = false;

        let filesSeleccionados = [];

        const dropzone = new Dropzone("#jsonDropzone", {
            paramName: "file[]",
            maxFilesize: 5, // MB
            acceptedFiles: ".json",
            uploadMultiple: true,
            autoProcessQueue: false,
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            parallelUploads: 10,
            init: function() {
                const btnConvertir = document.getElementById('convertir');
                const limpiarBtn = document.getElementById('limpiar');
                const loading = document.getElementById('loading');
                const fileList = document.getElementById('file-list');

                this.on("addedfile", function(file) {
                    filesSeleccionados.push(file);
                    btnConvertir.disabled = false;
                    limpiarBtn.style.display = "block";
                });

                this.on("removedfile", function(file) {
                    filesSeleccionados = filesSeleccionados.filter(f => f.name !== file.name);
                    if (filesSeleccionados.length === 0) {
                        btnConvertir.disabled = true;
                        limpiarBtn.style.display = "none";
                    }
                });

                btnConvertir.addEventListener('click', () => {
                    if (filesSeleccionados.length > 0) {
                        loading.style.display = "block";

                        this.options.url = ""; // Ajusta aquí tu ruta
                        this.processQueue();
                    }
                });

                this.on("successmultiple", function(files, response) {
                    const blob = new Blob([response], {
                        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = "json_convertido.xlsx";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    Toastify({
                        text: "¡Excel generado correctamente!",
                        duration: 3000,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#03C988",
                    }).showToast();

                    this.removeAllFiles();
                    filesSeleccionados = [];
                    btnConvertir.disabled = true;
                    limpiarBtn.style.display = "none";
                    loading.style.display = "none";
                });

                this.on("errormultiple", function(files, response) {
                    Toastify({
                        text: "Ocurrió un error con los archivos.",
                        duration: 3000,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#FF0000",
                    }).showToast();
                    loading.style.display = "none";
                });

                limpiarBtn.addEventListener('click', () => {
                    this.removeAllFiles();
                    filesSeleccionados = [];
                    btnConvertir.disabled = true;
                    limpiarBtn.style.display = "none";
                });
            }
        });
    </script>
@endsection
