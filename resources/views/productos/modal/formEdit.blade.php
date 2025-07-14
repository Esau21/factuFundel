 {{-- modal --}}
 <div class="modal fade" id="editProducto" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc modal-lg">
         <div class="modal-content">
             <div class="modal-body">
                 <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                     aria-label="Close"></button>
                 <div class="text-center mb-6">
                     <h4 class="mb-2">Editar producto</h4>
                     <div class="alert alert-danger fade show position-relative" role="alert">
                         <strong>Importante!</strong> Completa todos los campos.
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                             data-bs-dismiss="alert" aria-label="Close"></button>
                     </div>
                 </div>
                 <form id="editFormProduct" class="row g-6" onsubmit="return false" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="producto_id" id="producto_id">
                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="imagen">Imagen</label>
                         <div class="input-group input-group-merge">
                             <input id="imagen" name="imagen" class="form-control" type="file" />
                             <span class="input-group-text cursor-pointer">
                                 <span class="card-type me-n2"></span>
                             </span>
                         </div>
                         <div class="mt-2">
                             <img id="imagen-preview" src="{{ asset('img/camara1.png') }}" alt="Imagen actual"
                                 style="max-height: 150px;">
                         </div>
                     </div>


                     <div class="col-4 form-control-validation">
                         <label class="form-label w-100" for="codigo">Codigo producto</label>
                         <div class="input-group input-group-merge">
                             <input id="codigo" name="codigo" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>


                     <div class="col-4 form-control-validation">
                         <label class="form-label w-100" for="nombre">Nombre producto</label>
                         <div class="input-group input-group-merge">
                             <input id="nombre" name="nombre" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span class="card-type me-n2"></span></span>
                         </div>
                     </div>


                     <div class="col-4 form-control-validation">
                         <label class="form-label w-100" for="categoria_id">Categoria</label>
                         <div class="input-group input-group-merge">
                             <select name="categoria_id" id="categoria_id" class="form-control w-100 select2"
                                 data-dropdown-parent="#editProducto">
                                 <option value="">Elegir</option>
                                 @foreach ($categorias as $c)
                                     <option value="{{ $c->id }}">{{ $c->categoria_nombre }}</option>
                                 @endforeach
                             </select>
                         </div>
                     </div>

                     {{-- aca definimos el bien o servicio --}}
                     <div class="col-6 form-control-validation">
                         <label class="form-label w-100" for="item_id">Elegir el bien o servicio</label>
                         <div class="input-group input-group-merge">
                             <select name="item_id" id="item_id" class="form-select w-100"
                                 data-dropdown-parent="#editProducto">
                                 <option value="">Selecciona</option>
                                 @foreach ($items as $i)
                                     <option value="{{ $i->id }}">{{ $i->codigo }} | {{ $i->nombre }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>

                     {{-- aca definimos la unidad de medida --}}
                     <div class="col-6 form-control-validation">
                         <label class="form-label w-100" for="unidad_medida_id">Elegir la unidad de medida</label>
                         <div class="input-group input-group-merge">
                             <select name="unidad_medida_id" id="unidad_medida_id" class="select2 w-100 form-control"
                                 data-dropdown-parent="#editProducto">
                                 <option value="">Selecciona</option>
                                 @foreach ($unidades as $u)
                                     <option value="{{ $u->id }}">{{ $u->codigo }} | {{ $u->nombre }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>


                     <div class="col-12 form-control-validation">
                         <label class="form-label w-100" for="descripcion">Descripcion</label>
                         <div class="input-group input-group-merge">
                             <textarea name="descripcion" id="descripcion" cols="30" rows="1" class="form-control"
                                 placeholder="Ingresa tu descripcion"></textarea>
                             <span class="input-group-text cursor-pointer"><span
                                     class="card-type me-n2"></span></span>
                         </div>
                     </div>


                     <div class="col-3 form-control-validation">
                         <label class="form-label w-100" for="precio_compra">Precio compra</label>
                         <div class="input-group input-group-merge">
                             <input id="precio_compra" name="precio_compra" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span
                                     class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-3 form-control-validation">
                         <label class="form-label w-100" for="precio_venta">Precio venta</label>
                         <div class="input-group input-group-merge">
                             <input id="precio_venta" name="precio_venta" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span
                                     class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-3 form-control-validation">
                         <label class="form-label w-100" for="stock">Stock</label>
                         <div class="input-group input-group-merge">
                             <input id="stock" name="stock" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span
                                     class="card-type me-n2"></span></span>
                         </div>
                     </div>

                     <div class="col-3 form-control-validation">
                         <label class="form-label w-100" for="namerole">Stock minimo</label>
                         <div class="input-group input-group-merge">
                             <input id="stock_minimo" name="stock_minimo" class="form-control" type="text"
                                 placeholder="ejemplo" />
                             <span class="input-group-text cursor-pointer"><span
                                     class="card-type me-n2"></span></span>
                         </div>
                     </div>

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
     $('#imagen').on('change', function(event) {
         const input = event.target;

         if (input.files && input.files[0]) {
             const reader = new FileReader();

             reader.onload = function(e) {
                 $('#imagen-preview').attr('src', e.target.result);
             };

             reader.readAsDataURL(input.files[0]);
         }
     });

     $(document).on('click', '.btn-editar-producto', function() {
         let id = $(this).data('id');
         let imagen = $(this).data('imagen');
         let codigo = $(this).data('codigo');
         let nombre = $(this).data('nombre');
         let categoria_id = $(this).data('categoria_id');
         let item_id = $(this).data('item_id');
         let unidad_medida_id = $(this).data('unidad_medida_id');
         let descripcion = $(this).data('descripcion');
         let precio_compra = $(this).data('precio_compra');
         let precio_venta = $(this).data('precio_venta');
         let stock = $(this).data('stock');
         let stock_minimo = $(this).data('stock_minimo');
         let estado = $(this).data('estado');

         /* Rellenamos los campos del formulario con los valores obtenidos */
         $('#editProducto #producto_id').val(id);
         $('#editProducto #codigo').val(codigo);
         $('#editProducto #nombre').val(nombre);
         $('#editProducto #categoria_id').val(categoria_id);
         $('#editProducto #item_id').val(item_id);
         $('#editProducto #unidad_medida_id').val(unidad_medida_id);
         $('#editProducto #descripcion').val(descripcion);
         $('#editProducto #precio_compra').val(precio_compra);
         $('#editProducto #precio_venta').val(precio_venta);
         $('#editProducto #stock').val(stock);
         $('#editProducto #stock_minimo').val(stock_minimo);
         $('#editProducto #estado').val(estado);

         /* Si la imagen es proporcionada, mostrarla en el preview */
         if (imagen) {
             $('#imagen-preview').attr('src', imagen);
         }
     });

     /* Manejo del envío del formulario para actualizar el producto */
     $('#editFormProduct').on('submit', function(e) {
         e.preventDefault();

         let id = $('#producto_id').val();
         let url = `/update/producto/${id}`;
         let formData = new FormData(this);

         $.ajax({
             url: url,
             method: 'POST',
             processData: false,
             contentType: false,
             data: formData,
             headers: {
                 'X-CSRF-TOKEN': $('input[name="_token"]').val()
             },
             success: function(response) {
                 $('#editProducto').modal('hide');
                 $('#editFormProduct')[0].reset(); /* Reiniciamos el formulario */

                 /* Recargamos la tabla con los nuevos datos */
                 var table = $('#sysconta-datatable').DataTable();
                 table.ajax.reload(null, false);

                 /* Mostramos un mensaje de exito */
                 Toastify({
                     text: "Producto actualizado correctamente.",
                     className: "info",
                     style: {
                         background: "linear-gradient(to right, #3b3f5c, #3b3f5c)"
                     }
                 }).showToast();
             },
             error: function(e) {
                 /* Manejo de errores */
                 if (e.status === 422) {
                     let errors = e.responseJSON.errors;
                     let errorMessage = '';
                     $.each(errors, function(key, value) {
                         errorMessage += value.join('<br>');
                     });
                     $('#editProducto').modal('hide');
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
                     $('#editProducto').modal('hide');
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
