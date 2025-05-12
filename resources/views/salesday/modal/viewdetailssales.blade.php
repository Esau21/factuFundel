<!-- modal -->
<div class="modal fade" id="verSale" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered1 modal-simple modal-add-new-cc">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Detalles de venta </h4>
                    <div class="alert alert-dark fade show position-relative" role="alert">
                        <strong>Importante!</strong> a continuacion se le presentan los detalles de esta venta.
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                            data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <div id="venta-detalle-body">
                    <!-- Aquí se cargan los detalles vía AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Add New Credit Card Modal -->

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).on('click', '.btn-show-details', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/detalles/ventas/${id}`,
            type: 'GET',
            success: function(response) {
                let venta = response.venta;

                let html =
                    `
                    <p><strong>Venta #</strong> ${venta.id || 'sin id'} </p>
                    <p><strong>Cliente:</strong> ${venta.clientes?.nombre || 'sin cliente'} </p>
                    <p><strong>Vendedor:</strong> ${venta.users?.name || 'sin usuario'} </p>
                    <p><strong>Fecha emision:</strong> ${venta.fecha_venta || 'sin fecha'} </p>
                    <p><strong>Tipo de pago:</strong> ${venta.tipo_pago}</p>
                    <p><strong>Total:</strong> $${venta.total}</p>
                    <hr>
                    <h5>Productos:</h5>
                     <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;

                venta.detalles.forEach(detalle => {
                    html += `
                        <tr>
                            <td>${detalle.producto?.nombre || 'Producto eliminado'}</td>
                            <td>${detalle.cantidad}</td>
                            <td>$${detalle.precio_unitario}</td>
                            <td>$${detalle.sub_total}</td>
                        </tr>`;
                });

                html += `</tbody></table>`;

                $('#venta-detalle-body').html(html);
            },
            error: function() {
                $('#venta-detalle-body').html(
                    '<div class="alert alert-danger">Error al cargar los detalles.</div>');
            }
        });
    });
</script>
