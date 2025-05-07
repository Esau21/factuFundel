<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        /* Al escribir en el campo de búsqueda */
        $('#buscar_producto').on('keyup', function() {
            var query = $(this).val();
            if (query.length === 0) {
                $('#productSearchResults').hide();
                return;
            }
            $.ajax({
                url: "{{ route('sales.buscarProductos') }}",
                method: "GET",
                data: {
                    query: query
                },
                success: function(response) {
                    console.log("Respuesta AJAX:", response);

                    var productOptions = '';
                    if (response.length > 0) {
                        response.forEach(function(producto) {
                            productOptions += `
                                <div class="product-option" 
                                    data-id="${producto.id}" 
                                    data-nombre="${producto.nombre}" 
                                    data-imagen="${producto.imagen_url}" 
                                    data-precio="${producto.precio_venta}"
                                    style="display: flex; align-items: center; padding: 5px; cursor: pointer; border-bottom: 1px solid #eee;">
                                    <img class="img-fluid" src="${producto.imagen_url}" alt="${producto.nombre}" style="width: 60px; height: 60px; margin-right: 10px;">
                                    <span>${producto.nombre} - $${producto.precio_venta}</span>
                                </div>
                            `;
                        });
                        $('#productSearchResults').html(productOptions).show();
                    } else {
                        $('#productSearchResults').hide();
                    }
                },
                error: function() {
                    console.error("Error en la búsqueda de productos.");
                }
            });
        });

        $(document).on('click', '.product-option', function() {
            var productoId = $(this).data('id');
            var productoNombre = $(this).data('nombre');
            var productoPrecio = $(this).data('precio');
            var productoImagen = $(this).data('imagen');

            console.log("Imagen seleccionada:", productoImagen);

            var newRow = `
            <tr>
                <td>${productoId}</td>
                <td>
                ${productoNombre}
                ${productoImagen ? `<img src="${productoImagen}" alt="Imagen" width="60">` : ''}
                </td>
                <td><input type="number" class="form-control precio_unitario" name="precio_unitario[]" value="${productoPrecio}" step="0.01"></td>
                <td>
                    <input type="number" class="form-control cantidad" name="cantidad[]" value="1" min="1">
                </td>
                <td><input type="text" class="form-control sub_total" name="sub_total[]" value="${parseFloat(productoPrecio).toFixed(2)}" readonly></td>
                <td>
                    <button type="button" class="btn btn-danger remove-row">
                        <i class="bx bx-trash"></i>
                    </button>
                    <button type="button" class="btn btn-warning decrease-qty">
                        <i class="bx bx-minus"></i>
                    </button>
                    <button type="button" class="btn btn-dark increase-qty">
                        <i class="bx bx-plus"></i>
                    </button>
                </td>
            </tr>
            `;

            $('#productRows').append(newRow);
            $('#productSearchResults').hide();
            $('#buscar_producto').val('');
            actualizarTotal();
        });

        /* Aumentamos la cantidad */
        $(document).on('click', '.increase-qty', function() {
            var row = $(this).closest('tr');
            var cantidad = row.find('.cantidad');
            var newCantidad = parseInt(cantidad.val()) + 1;
            cantidad.val(newCantidad);

            actualizarSubtotal(row);
            actualizarTotal();
        });

        /* Disminuye la cantidad (no puede ser menor a 1) */
        $(document).on('click', '.decrease-qty', function() {
            var row = $(this).closest('tr');
            var cantidad = row.find('.cantidad');
            var newCantidad = Math.max(parseInt(cantidad.val()) - 1,
                1); // Asegura que la cantidad no sea menor a 1
            cantidad.val(newCantidad);

            actualizarSubtotal(row);
            actualizarTotal();
        });

        // Recalcula el subtotal de la fila
        function actualizarSubtotal(row) {
            var cantidad = parseFloat(row.find('.cantidad').val()) || 0;
            var precio = parseFloat(row.find('.precio_unitario').val()) || 0;
            var subtotal = cantidad * precio;

            row.find('.sub_total').val(subtotal.toFixed(2));
        }

        // Elimina fila
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            actualizarTotal();
        });

        // Función para actualizar el total general y cantidad total
        function actualizarTotal() {
            var total = 0;
            var totalItems = 0;

            $('.sub_total').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            $('.cantidad').each(function() {
                totalItems += parseInt($(this).val()) || 0;
            });

            $('#totalAmount').text(total.toFixed(2));
            $('#totalItems').text(totalItems);
        }
    });
</script>
