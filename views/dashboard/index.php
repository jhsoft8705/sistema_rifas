<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">
<?php
require_once __DIR__ . "/../../config/Enrutamiento.php";
?>
<head>
    <meta charset="utf-8" />
    <title>Dashboard | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Gestión de Rifas" name="description" />
    <?php require_once __DIR__. '/../components/head.php' ?>
    
    <!-- ApexCharts CSS -->
    <link href="assets/libs/apexcharts/apexcharts.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div id="layout-wrapper">
        <?php require_once __DIR__.'/../components/navbar.php' ?>
        <?php require_once __DIR__.'/../components/appmenu.php' ?>
        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Dashboard</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/views/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Inicio</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs - Ventas & Tickets -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-shopping-cart-line me-2"></i>Ventas & Tickets</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-primary">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-primary">
                                                                <span class="avatar-title bg-primary rounded-circle">
                                                                    <i class="ri-ticket-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Tickets Vendidos Hoy</p>
                                                            <h4 class="my-1" id="kpi_tickets_vendidos_hoy">0</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-success">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-success">
                                                                <span class="avatar-title bg-success rounded-circle">
                                                                    <i class="ri-money-dollar-circle-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Ingresos Hoy</p>
                                                            <h4 class="my-1" id="kpi_ingresos_hoy">S/ 0.00</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-info">
                                                                <span class="avatar-title bg-info rounded-circle">
                                                                    <i class="ri-calendar-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Ingresos del Mes</p>
                                                            <h4 class="my-1" id="kpi_ingresos_mes">S/ 0.00</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-warning">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-warning">
                                                                <span class="avatar-title bg-warning rounded-circle">
                                                                    <i class="ri-bar-chart-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Ticket Promedio</p>
                                                            <h4 class="my-1" id="kpi_ticket_promedio">S/ 0.00</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs - Estado Operativo -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-settings-3-line me-2"></i>Estado Operativo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-warning">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-warning">
                                                                <span class="avatar-title bg-warning rounded-circle">
                                                                    <i class="ri-time-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Pendientes Validación</p>
                                                            <h4 class="my-1" id="kpi_tickets_pendientes">0</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-danger">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-danger">
                                                                <span class="avatar-title bg-danger rounded-circle">
                                                                    <i class="ri-close-circle-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Pagos Rechazados Hoy</p>
                                                            <h4 class="my-1" id="kpi_pagos_rechazados">0</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-warning">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-warning">
                                                                <span class="avatar-title bg-warning rounded-circle">
                                                                    <i class="ri-alert-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Tickets por Expirar</p>
                                                            <h4 class="my-1" id="kpi_tickets_expirar">0</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-6">
                                            <div class="card border border-info">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-info">
                                                                <span class="avatar-title bg-info rounded-circle">
                                                                    <i class="ri-group-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Personas Únicas</p>
                                                            <h4 class="my-1" id="kpi_personas_unicas">0</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs - Rifas -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-gift-line me-2"></i>Rifas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-4 col-md-6">
                                            <div class="card border border-primary">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-primary">
                                                                <span class="avatar-title bg-primary rounded-circle">
                                                                    <i class="ri-fire-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Rifas Activas</p>
                                                            <h4 class="my-1" id="kpi_rifas_activas">0</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-6">
                                            <div class="card border border-success">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-success">
                                                                <span class="avatar-title bg-success rounded-circle">
                                                                    <i class="ri-trophy-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Rifa Más Vendida</p>
                                                            <h5 class="my-1 text-truncate" id="kpi_rifa_mas_vendida" style="max-width: 200px;">-</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-6">
                                            <div class="card border border-warning">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-sm rounded bg-soft-warning">
                                                                <span class="avatar-title bg-warning rounded-circle">
                                                                    <i class="ri-speed-line font-22 text-white"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <p class="text-uppercase fw-medium text-muted mb-0">Rifa Menor Avance</p>
                                                            <h5 class="my-1 text-truncate" id="kpi_rifa_menor_avance" style="max-width: 200px;">-</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row">
                        <!-- Ventas en el Tiempo -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-line-chart-line me-2"></i>Ventas en el Tiempo</h5>
                                    <div class="d-flex gap-2 mt-2">
                                        <button class="btn btn-sm btn-outline-primary" onclick="cargarVentasTiempo(7)">7 días</button>
                                        <button class="btn btn-sm btn-outline-primary" onclick="cargarVentasTiempo(30)">30 días</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="chart_ventas_tiempo" data-colors='["--vz-primary"]'></div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado de Tickets -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-pie-chart-line me-2"></i>Estado de Tickets</h5>
                                </div>
                                <div class="card-body">
                                    <div id="chart_estado_tickets" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Avance de Rifas -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-bar-chart-box-line me-2"></i>Avance de Rifas</h5>
                                </div>
                                <div class="card-body">
                                    <div id="chart_avance_rifas" data-colors='["--vz-primary"]'></div>
                                </div>
                            </div>
                        </div>

                        <!-- Canales de Venta -->
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-pie-chart-2-line me-2"></i>Canales de Venta</h5>
                                </div>
                                <div class="card-body">
                                    <div id="chart_canales_venta" data-colors='["--vz-primary", "--vz-success", "--vz-info", "--vz-warning"]'></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tablas Rápidas -->
                    <div class="row">
                        <!-- Últimos Movimientos -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-history-line me-2"></i>Últimos Movimientos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-nowrap align-middle mb-0" id="table_ultimos_movimientos">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Código</th>
                                                    <th>Persona</th>
                                                    <th>Rifa</th>
                                                    <th>Monto</th>
                                                    <th>Estado</th>
                                                    <th>Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_ultimos_movimientos">
                                                <tr>
                                                    <td colspan="7" class="text-center">Cargando...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Últimos Ganadores -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-trophy-fill me-2"></i>Últimos Ganadores</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-nowrap align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Rifa</th>
                                                    <th>Premio</th>
                                                    <th>Ganador</th>
                                                    <th>Número</th>
                                                    <th>Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_ultimos_ganadores">
                                                <tr>
                                                    <td colspan="5" class="text-center">Cargando...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla Tickets Aprobados -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-checkbox-circle-line me-2"></i>Tickets Aprobados</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-nowrap align-middle mb-0" id="table_tickets_aprobados">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Persona</th>
                                                    <th>Rifa</th>
                                                    <th>Monto</th>
                                                    <th>Canal</th>
                                                    <th>Fecha Compra</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_tickets_aprobados">
                                                <tr>
                                                    <td colspan="6" class="text-center">Cargando...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php require_once __DIR__.'/../components/footer.php' ?>
        </div>
    </div>

    <?php require_once __DIR__.'/../components/js.php'?>

    <!-- ApexCharts JS -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Dashboard JS -->
    <script src="<?= Enrutamiento::dominio() ?>/views/dashboard/dashboard.js"></script>

    <!-- Dashboard Init -->
    <script>
        // Proteger ruta - Requiere autenticación
        if (!Auth.isAuthenticated()) {
            window.location.href = window.BASE_URL;
        }

        // Manejar errores de elementos null del app.js
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes("Cannot read properties of null")) {
                console.warn('Elemento del DOM no encontrado - ignorando:', e.message);
                e.preventDefault();
                return true;
            }
        }, true);

        console.log('✓ Dashboard cargado correctamente');
    </script>

</body>
</html>
