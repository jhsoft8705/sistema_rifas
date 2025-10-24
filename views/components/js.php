<?php
require_once __DIR__ . "/../../config/Enrutamiento.php";
?>
<!-- JAVASCRIPT -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Configuración global de rutas desde PHP -->
<script>
    // Obtener ruta base desde PHP Enrutamiento
    window.BASE_URL = '<?= Enrutamiento::dominio() ?>';
    window.API_BASE_URL = window.BASE_URL + '/api';
/*     console.log('Rutas configuradas:', { BASE_URL: window.BASE_URL, API_BASE_URL: window.API_BASE_URL });
 */</script>

<!-- Auth.js - Helper Global de Autenticación -->
<script src="<?= Enrutamiento::dominio() ?>/helpers/Auth.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- SweetAlert2 -->
<script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/js/pages/sweetalerts.init.js"></script>

<!-- jQuery Toast Plugin -->
<script src="node_modules/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

<!-- Flatpickr -->
 <script src="node_modules/flatpickr/dist/flatpickr.min.js"></script>
<script src="node_modules/flatpickr/dist/l10n/es.js"></script>  

<!-- Bootstrap & Plugins -->
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="assets/js/plugins.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>


 