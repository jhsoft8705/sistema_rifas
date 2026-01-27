<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Registro de Ventas | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Registro de ventas de tickets administrativo" name="description" />
    <?php require_once __DIR__ . "/../../components/head.php"; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php require_once __DIR__ . "/../../components/navbar.php"; ?>
        <?php require_once __DIR__ . "/../../components/appmenu.php"; ?>

        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">
                                    <i class="ri-shopping-cart-line me-2"></i>Registro de Ventas
                                </h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/admin-dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/admin-rifas">Rifas</a></li>
                                        <li class="breadcrumb-item active">Ventas</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Tabs para alternar entre Rifas y Ventas -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#rifas-disponibles" role="tab" aria-selected="true">
                                        <i class="ri-ticket-2-line me-1"></i> Rifas Disponibles
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#ventas-realizadas" role="tab" aria-selected="false">
                                        <i class="ri-shopping-bag-line me-1"></i> Ventas Realizadas
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Tab: Rifas Disponibles -->
                                <div class="tab-pane fade show active" id="rifas-disponibles" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="ri-ticket-2-line me-2"></i>Rifas Disponibles para Venta
                                            </h5>

                                            <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                                <div class="d-flex flex-wrap flex-grow-1 gap-3">
                                                    <div class="input-group" style="max-width: 220px; min-width: 180px;">
                                                        <select id="filtro_estado_venta" class="form-select"
                                                            style="min-height: 40px; font-size: 0.9rem;"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="Filtrar rifas por estado">
                                                            <option value="">Todos los estados</option>
                                                            <option value="EN_VENTA">En venta</option>
                                                            <option value="PUBLICADA">Publicada</option>
                                                        </select>
                                                    </div>
                                                    <button type="button" id="btn_filtrar_ventas" class="btn btn-outline-info"
                                                        style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Filtrar rifas por estado">
                                                        <i class="ri-filter-line me-1"></i>Filtrar
                                                    </button>
                                                    <button type="button" id="btn_recargar_ventas" class="btn btn-outline-warning"
                                                        style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="Recargar tabla y limpiar filtros">
                                                        <i class="ri-refresh-line me-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="tabla_rifas_ventas"
                                                    class="table table-hover align-middle table-nowrap mb-0" style="width:100% !important;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th scope="col" class="text-center">Acciones</th>
                                                            <th scope="col">Código</th>
                                                            <th scope="col">Nombre</th>
                                                            <th scope="col">Premio principal</th>
                                                            <th scope="col">Precio ticket</th>
                                                            <th scope="col">Disponibles</th>
                                                            <th scope="col">Vendidos</th>
                                                            <th scope="col">Estado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Los datos se cargarán dinámicamente via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Ventas Realizadas -->
                                <div class="tab-pane fade" id="ventas-realizadas" role="tabpanel">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="ri-shopping-bag-line me-2"></i>Ventas Realizadas
                                            </h5>

                                            <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                                <div class="d-flex flex-wrap flex-grow-1 gap-3">
                                                    <div class="input-group" style="max-width: 200px; min-width: 180px;">
                                                        <select id="filtro_rifa_ventas" class="form-select"
                                                            style="min-height: 40px; font-size: 0.9rem;"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="Filtrar por rifa">
                                                            <option value="">Todas las rifas</option>
                                                        </select>
                                                    </div>
                                                    <div class="input-group" style="max-width: 200px; min-width: 180px;">
                                                        <select id="filtro_estado_ventas" class="form-select"
                                                            style="min-height: 40px; font-size: 0.9rem;"
                                                            data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                            title="Filtrar por estado">
                                                            <option value="">Todos los estados</option>
                                                            <option value="PENDIENTE_PAGO">Pendiente pago</option>
                                                            <option value="PAGO_SUBIDO">Pago subido</option>
                                                            <option value="VALIDANDO">Validando</option>
                                                            <option value="APROBADO">Aprobado</option>
                                                            <option value="RECHAZADO">Rechazado</option>
                                                            <option value="PARTICIPANDO">Participando</option>
                                                            <option value="GANADOR">Ganador</option>
                                                        </select>
                                                    </div>
                                                    <button type="button" id="btn_filtrar_ventas_listado" class="btn btn-outline-info"
                                                        style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;">
                                                        <i class="ri-filter-line me-1"></i>Filtrar
                                                    </button>
                                                    <button type="button" id="btn_recargar_ventas_listado" class="btn btn-outline-warning"
                                                        style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;">
                                                        <i class="ri-refresh-line me-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="tabla_ventas_realizadas"
                                                    class="table table-hover align-middle table-nowrap mb-0" style="width:100% !important;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th scope="col" class="text-center">Acciones</th>
                                                            <th scope="col">Código Ticket</th>
                                                            <th scope="col">Números Comprados</th>
                                                            <th scope="col">Cliente</th>
                                                            <th scope="col">Documento</th>
                                                            <th scope="col">Rifa</th>
                                                            <th scope="col">Precio</th>
                                                            <th scope="col">Estado</th>
                                                            <th scope="col">Fecha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Los datos se cargarán dinámicamente via AJAX -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once __DIR__ . '/../../components/footer.php'; ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/modal_venta.php'; ?>
    <?php require_once __DIR__ . '/../../components/js.php'; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/rifas/ventas/ventas.js"></script>
    
    <!-- Librería para generar PDFs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>

</html>

