<?php
require_once __DIR__ . "/../../../config/Enrutamiento.php";
?>
<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Reporte Recaudación | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Reporte de dinero recaudado por rifa y ganadores" name="description" />
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
                                <h4 class="mb-sm-0">Reporte Recaudación por Rifa</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Reporte Recaudación</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-file-chart-line me-2"></i>Filtros
                                    </h5>
                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3 align-items-end">
                                            <div class="w-auto" style="max-width: 200px; min-width: 180px;">
                                                <label class="form-label small">Fecha desde</label>
                                                <input type="date" id="filtro_fecha_desde" class="form-control" style="min-height: 40px;">
                                            </div>
                                            <div class="w-auto" style="max-width: 200px; min-width: 180px;">
                                                <label class="form-label small">Fecha hasta</label>
                                                <input type="date" id="filtro_fecha_hasta" class="form-control" style="min-height: 40px;">
                                            </div>
                                            <div class="w-auto" style="max-width: 280px; min-width: 220px;">
                                                <label class="form-label small">Rifa</label>
                                                <select id="filtro_rifa" class="form-select" style="min-height: 40px; font-size: 0.9rem;">
                                                    <option value="">Seleccione una rifa...</option>
                                                </select>
                                            </div>
                                            <button class="btn btn-outline-info" id="btn_filtrar_reporte" style="min-height: 40px; padding: 0.5rem 1rem;">
                                                <i class="ri-filter-line me-1"></i>Filtrar
                                            </button>
                                            <button class="btn btn-outline-warning" id="btn_recargar_reporte" style="min-height: 40px; padding: 0.5rem 1rem;" title="Recargar">
                                                <i class="ri-refresh-line me-1"></i>
                                            </button>
                                            <button class="btn btn-success" id="btn_excel_reporte" style="min-height: 40px; padding: 0.5rem 1rem;" title="Descargar Excel">
                                                <i class="ri-file-excel-2-line me-1"></i>Descargar Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div id="resumen_recaudacion" class="mb-4" style="display: none;">
                                        <h6 class="text-muted mb-2"><i class="ri-money-dollar-circle-line me-1"></i>Resumen de recaudación</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm mb-0" id="tabla_resumen">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Rifa</th>
                                                        <th class="text-end">Total recaudado</th>
                                                        <th class="text-center">Cant. tickets</th>
                                                        <th>Rango fechas</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody_resumen"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <h6 class="text-muted mb-2"><i class="ri-trophy-line me-1"></i>Ganadores de la rifa</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-nowrap mb-0" id="tabla_ganadores">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nombre</th>
                                                    <th>Documento</th>
                                                    <th>Premio</th>
                                                    <th>Nº ganador</th>
                                                    <th>Fecha</th>
                                                    <th>Tickets</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
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

    <?php require_once __DIR__ . '/../../components/js.php'; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/reportes/recaudacion/recaudacion.js"></script>
</body>

</html>
