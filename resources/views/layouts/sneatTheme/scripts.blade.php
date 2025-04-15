 <!-- Core JS -->

 <script src="{{ asset('../assets/vendor/libs/jquery/jquery.js') }}"></script>

 <script src="{{ asset('../assets/vendor/libs/popper/popper.js') }}"></script>
 <script src="{{ asset('../assets/vendor/js/bootstrap.js') }}"></script>

 <script src="{{ asset('../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

 <script src="{{ asset('../assets/vendor/js/menu.js') }}"></script>

 <!-- endbuild -->

 <!-- Vendors JS -->
 <script src="{{ asset('../assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

 <!-- Main JS -->

 <script src="{{ asset('../assets/js/main.js') }}"></script>

 <!-- Page JS -->
 <script src="{{ asset('../assets/js/dashboards-analytics.js') }}"></script>

 <!-- Place this tag before closing body tag for github widget button. -->
 <script async defer src="https://buttons.github.io/buttons.js"></script>

 <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

 <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

 <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 <script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2').each(function() {
                $(this).select2({
                    width: '100%'
                });
            });
        });
    });
</script>

