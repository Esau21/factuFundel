<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        //seleccionamos el tipo de pago
        $('#tipo_pago').on('change', function() {
            var tipoPago = $(this).val();
            //Ocultamos todos los campos
            $('#cheque_fields, #transferencia_fields, #efectivo_fields').hide();

            if (tipoPago === 'cheque') {
                $('#cheque_fields').show();
            } else if (tipoPago === 'transferencia') {
                $('#transferencia_fields').show();
            } else if (tipoPago === 'efectivo') {
                $('#efectivo_fields').show();
            } else if (tipoPago === 'mixto_cheque_efectivo') {
                $('#cheque_fields').show();
                $('#efectivo_fields').show();
            } else if (tipoPago === 'mixto_transferencia_efectivo') {
                $('#transferencia_fields').show();
                $('#efectivo_fields').show();
            }
        });

        function actualizarTotalPago() {
            let totalVenta = parseFloat($('#totalAmount').text()) || 0;

            let montoCheque = parseFloat($('#monto').val()) || 0;
            let montoTransferencia = parseFloat($('#monto_transferencia').val()) || 0;
            let montoEfectivo = parseFloat($('#cash').val()) || 0;

            // Total pagado (suma de todos)
            let totalPagado = montoCheque + montoTransferencia + montoEfectivo;

            // Monto a cubrir con efectivo = totalVenta - (cheque + transferencia)
            let efectivoNecesario = totalVenta - (montoCheque + montoTransferencia);

            // Cambio solo si efectivo entregado > efectivo necesario, sino 0
            let cambio = montoEfectivo > efectivoNecesario ? montoEfectivo - efectivoNecesario : 0;

            // Mostrar el cambio
            $('#changeAmount').text(cambio.toFixed(2));
            $('#cambioInput').val(cambio);

            // Mensaje y habilitar botón según si el pago cubre total
            if (totalPagado < totalVenta) {
                $('#mensajePago').text('El pago total es menor al total de la venta.');
                $('#guardarVenta').prop('disabled', true);
            } else {
                $('#mensajePago').text('');
                $('#guardarVenta').prop('disabled', false);
            }
        }

        $('#monto, #monto_transferencia, #cash').on('input', actualizarTotalPago);

        // Busqueda de productos
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
                            </div>`;
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

        // Seleccionar producto
        $(document).on('click', '.product-option', function() {
            var productoId = $(this).data('id');
            var productoNombre = $(this).data('nombre');
            var productoPrecio = $(this).data('precio');
            var productoImagen = $(this).data('imagen');

            var newRow = `
        <tr>
            <td>${productoId}</td>
            <td>
                ${productoNombre}
                ${productoImagen ? `<img src="${productoImagen}" alt="Imagen" width="60">` : ''}
            </td>
            <td><input type="number" class="form-control precio_unitario" name="precio_unitario[]" value="${productoPrecio}" step="0.01"></td>
            <td><input type="number" class="form-control cantidad" name="cantidad[]" value="1" min="1"></td>
            <td><input type="text" class="form-control sub_total" name="sub_total[]" value="${parseFloat(productoPrecio).toFixed(2)}" readonly></td>
            <td>
                <button type="button" class="btn btn-danger remove-row"><i class="bx bx-trash"></i></button>
                <button type="button" class="btn btn-warning decrease-qty"><i class="bx bx-minus"></i></button>
                <button type="button" class="btn btn-dark increase-qty"><i class="bx bx-plus"></i></button>
            </td>
        </tr>`;
            $('#productRows').append(newRow);
            $('#productSearchResults').hide();
            $('#buscar_producto').val('');
            actualizarSubtotal();
            actualizarTotal();
        });

        // Cantidad modificada
        $(document).on('click', '.increase-qty, .decrease-qty', function() {
            var row = $(this).closest('tr');
            var cantidad = row.find('.cantidad');
            if ($(this).hasClass('increase-qty')) {
                cantidad.val(parseInt(cantidad.val()) + 1);
            } else {
                cantidad.val(Math.max(parseInt(cantidad.val()) - 1, 1));
            }
            actualizarSubtotal();
            actualizarTotal();
            Toastify({
                text: "Cantidad actualizada.",
                className: "success",
                style: {
                    background: "linear-gradient(to right, #3b3f5c, #3b3f5c)"
                }
            }).showToast();
        });

        $(document).on('change keyup', '.cantidad, .precio_unitario', function() {
            actualizarSubtotal();
            actualizarTotal();
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            actualizarSubtotal();
            actualizarTotal();
            Toastify({
                text: "Producto eliminado.",
                className: "success",
                style: {
                    background: "linear-gradient(to right, #3b3f5c, #3b3f5c)"
                }
            }).showToast();
        });

        // Aplicar descuento
        $('#descuento_porcentaje, #descuento_en_dolar').on('input change', function() {
            actualizarSubtotal();
            actualizarTotal();
        });

        $('#cash').on('input', function() {
            actualizarTotal();
        });

        function actualizarSubtotal() {
            let totalBruto = 0;
            let descuentoFijo = parseFloat($('#descuento_en_dolar').val()) || 0;
            let porcentaje = parseFloat($('#descuento_porcentaje').val()) || 0;

            // Calcular el total bruto y el subtotal bruto de cada producto
            $("#productRows tr").each(function() {
                const row = $(this);
                const precio = parseFloat(row.find('.precio_unitario').val()) || 0;
                const cantidad = parseInt(row.find('.cantidad').val()) || 0;
                const subTotalBruto = precio * cantidad;
                row.data('sub_total_bruto', subTotalBruto);
                totalBruto += subTotalBruto;
            });

            // Aplicar descuento por porcentaje y fijo sobre los productos
            $("#productRows tr").each(function() {
                const row = $(this);
                const subTotalBruto = row.data('sub_total_bruto');
                let subTotalConDescuento = subTotalBruto;

                // Descuento por porcentaje
                if (porcentaje > 0) {
                    subTotalConDescuento -= subTotalBruto * (porcentaje / 100);
                }

                // Descuento fijo (se reparte proporcionalmente entre los productos)
                if (descuentoFijo > 0 && totalBruto > 0) {
                    const proporcion = subTotalBruto / totalBruto;
                    subTotalConDescuento -= descuentoFijo * proporcion;
                }

                // Establecemos el valor con descuento en el campo de "sub_total"
                row.find('.sub_total').val(subTotalConDescuento.toFixed(2));
            });
        }



        function actualizarTotal() {
            let total = 0;
            let totalItems = 0;
            $('.sub_total').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('.cantidad').each(function() {
                totalItems += parseInt($(this).val()) || 0;
            });

            $('#totalAmount').text(total.toFixed(2));
            $('#totalItems').text(totalItems);

            let efectivo = parseFloat($('#cash').val()) || 0;
            let cambio = efectivo - total;
            $('#changeAmount').text(cambio >= 0 ? cambio.toFixed(2) : "0.00");
            $('#cambioInput').val(cambio >= 0 ? cambio.toFixed(2) : 0);
        }

        //escuchamos el evento de la tecla F5
        $(document).on('keydown', function(e) {
            if (e.which === 116) {
                e.preventDefault();
                $('#guardarVenta').click();
            }
        });

        // Guardamos la venta
        $(document).on('click', '#guardarVenta', function(e) {
            e.preventDefault();
            var producto_id = [],
                cantidad = [],
                precio_unitario = [],
                sub_total = [];

            var tipo_pago = $('#tipo_pago').val();
            var cheque_bancario_id = null;
            var datos_cheque = {};
            var datos_transferencia = {};

            if (tipo_pago === 'cheque') {
                datos_cheque = {
                    numero_cheque: $('#numero_cheque').val(),
                    cuenta_bancaria_id: $('#cuenta_id').val(),
                    fecha_emision: $('#fecha_emision').val(),
                    monto: $('#monto').val(),
                    estado: $('#estado').val(),
                    observaciones: $('#observaciones').val(),
                    correlativo: $('#correlativo').val()
                };
            }

            if (tipo_pago === 'transferencia') {
                datos_transferencia = {
                    cuenta_bancaria_id: $('#cuenta_bancaria_id').val(),
                    monto_transferencia: $('#monto_transferencia').val()
                };
            }


            $("#productRows tr").each(function() {
                var id = $(this).find('td:first').text().trim();
                var cant = parseInt($(this).find('.cantidad').val());
                var precio = parseFloat($(this).find('.precio_unitario').val());
                var subtotal = parseFloat($(this).find('.sub_total')
                    .val()); // Verifica que este valor tenga el descuento

                if (id && !isNaN(cant) && !isNaN(precio) && !isNaN(subtotal)) {
                    producto_id.push(id);
                    cantidad.push(cant);
                    precio_unitario.push(precio);
                    sub_total.push(subtotal); // Este es el valor con descuento
                }
            });

            var cliente_id = $('#cliente_id').val();
            var efectivo = parseFloat($('#cash').val());
            var total = parseFloat($('#totalAmount').text());
            var tipo_pago = $('#tipo_pago').val();
            var cambio = $('#cambioInput').val();

            var descuento_porcentaje = parseFloat($('#descuento_porcentaje').val().replace('%', '')) ||
                0;
            var descuento_en_dolar = $('#descuento_en_dolar').val();

            $("#guardarVenta").prop('disabled', true);

            $.ajax({
                url: "{{ route('sales.generarSale') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    cliente_id,
                    efectivo,
                    total,
                    tipo_pago,
                    cambio,
                    producto_id,
                    cantidad,
                    precio_unitario,
                    sub_total, // Aquí enviamos el valor con descuento
                    descuento_porcentaje,
                    descuento_en_dolar,
                    // Campos planos de cheque (solo si aplica)
                    ...(tipo_pago === 'cheque' ? {
                        cliente_id,
                        numero_cheque: $('#numero_cheque').val(),
                        cuenta_bancaria_id: $('#cuenta_id').val(),
                        fecha_emision: $('#fecha_emision').val(),
                        monto: $('#monto').val(),
                        estado: $('#estado').val(),
                        observaciones: $('#observaciones').val(),
                        correlativo: $('#correlativo').val()
                    } : {}),

                    ...(tipo_pago === 'mixto_cheque_efectivo' ? {
                        cliente_id,
                        numero_cheque: $('#numero_cheque').val(),
                        cuenta_bancaria_id: $('#cuenta_id').val(),
                        fecha_emision: $('#fecha_emision').val(),
                        monto: $('#monto').val(),
                        estado: $('#estado').val(),
                        observaciones: $('#observaciones').val(),
                        correlativo: $('#correlativo').val(),
                        efectivo,
                        cambio
                    } : {}),

                    // Campos planos de transferencia (solo si aplica)
                    ...(tipo_pago === 'transferencia' ? {
                        cuenta_bancaria_id: $('#cuenta_bancaria_id').val(),
                        monto_transferencia: $('#monto_transferencia').val()
                    } : {}),

                    ...(tipo_pago === 'mixto_transferencia_efectivo' ? {
                        cuenta_bancaria_id: $('#cuenta_bancaria_id').val(),
                        monto_transferencia: $('#monto_transferencia').val(),
                        efectivo,
                        cambio
                    } : {})

                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    Toastify({
                        text: "Venta guardada exitosamente.",
                        className: "success",
                        style: {
                            background: "linear-gradient(to right, #28a745, #218838)"
                        }
                    }).showToast();
                    var url = URL.createObjectURL(response);
                    window.open(url, '_blank');
                    setTimeout(() => {
                        window.location.href = "{{ route('sales.index') }}";
                    }, 2000);
                },
                error: function() {
                    Toastify({
                        text: "Error al guardar la venta.",
                        className: "error",
                        style: {
                            background: "linear-gradient(to right, #dc3545, #c82333)"
                        }
                    }).showToast();
                    $("#guardarVenta").prop('disabled', false);
                }
            });
        });


        //escuchamos el evento de tecla f4
        $(document).on('keydown', function(e) {
            if (e.key === "F4") {
                e.preventDefault();
                $('#guardarCotizacion').click();
            }
        });

        // Guardamos la cotizacion
        $(document).on('click', '#guardarCotizacion', function(e) {
            e.preventDefault();
            var producto_id = [],
                cantidad = [],
                precio_unitario = [],
                sub_total = [];

            $("#productRows tr").each(function() {
                var id = $(this).find('td:first').text().trim();
                var cant = parseInt($(this).find('.cantidad').val());
                var precio = parseFloat($(this).find('.precio_unitario').val());
                var subtotal = parseFloat($(this).find('.sub_total')
                    .val());

                if (id && !isNaN(cant) && !isNaN(precio) && !isNaN(subtotal)) {
                    producto_id.push(id);
                    cantidad.push(cant);
                    precio_unitario.push(precio);
                    sub_total.push(subtotal); // Este es el valor con descuento
                }
            });

            var cliente_id = $('#cliente_id').val();
            var efectivo = parseFloat($('#cash').val());
            var total = parseFloat($('#totalAmount').text());
            var tipo_pago = 'COTIZACION';
            var cambio = $('#cambioInput').val();

            var descuento_porcentaje = parseFloat($('#descuento_porcentaje').val().replace('%', '')) ||
                0;
            var descuento_en_dolar = $('#descuento_en_dolar').val();

            $("#guardarCotizacion").prop('disabled', true);


            $.ajax({
                url: "{{ route('sales.generarCotizacion') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    cliente_id,
                    efectivo,
                    total,
                    tipo_pago,
                    cambio,
                    producto_id,
                    cantidad,
                    precio_unitario,
                    sub_total, // Aquí estás enviando el valor con descuento
                    descuento_porcentaje,
                    descuento_en_dolar
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    Toastify({
                        text: "Cotizacion guardada exitosamente.",
                        className: "success",
                        style: {
                            background: "linear-gradient(to right, #28a745, #218838)"
                        }
                    }).showToast();
                    var url = URL.createObjectURL(response);
                    window.open(url, '_blank');
                    setTimeout(() => {
                        window.location.href = "{{ route('sales.index') }}";
                    }, 2000);
                },
                error: function() {
                    Toastify({
                        text: "Error al guardar la venta.",
                        className: "error",
                        style: {
                            background: "linear-gradient(to right, #dc3545, #c82333)"
                        }
                    }).showToast();
                    $("#guardarCotizacion").prop('disabled', false);
                }
            });

        });

        // Subtotal y total inicial
        actualizarSubtotal();
        actualizarTotal();
    });
</script>
