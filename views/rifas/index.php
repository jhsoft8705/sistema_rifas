<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Gestión de Rifas y Sorteos | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Administración de rifas y sorteos" name="description" />
    <?php require_once __DIR__ . "/../components/head.php"; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php require_once __DIR__ . "/../components/navbar.php"; ?>
        <?php require_once __DIR__ . "/../components/appmenu.php"; ?>

        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Gestión de Rifas y Sorteos</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Rifas</li>
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
                                        <i class="ri-ticket-2-line me-2"></i>Filtros y Acciones
                                    </h5>

                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3 align-items-center">
                                            <div class="w-auto" style="max-width: 240px; min-width: 200px;">
                                                <select id="filtro_sede_rifa" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar rifas por sede">
                                                    <option value="">Todas las sedes</option>
                                                </select>
                                            </div>
                                            <div class="w-auto" style="max-width: 220px; min-width: 180px;">
                                                <select id="filtro_estado_rifa" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar rifas por estado">
                                                    <option value="">Todos los estados</option>
                                                    <option value="BORRADOR">Borrador</option>
                                                    <option value="PUBLICADA">Publicada</option>
                                                    <option value="EN_VENTA">En venta</option>
                                                    <option value="CERRADA">Cerrada</option>
                                                    <option value="SORTEO_REALIZADO">Sorteo realizado</option>
                                                    <option value="FINALIZADA">Finalizada</option>
                                                    <option value="CANCELADA">Cancelada</option>
                                                </select>
                                            </div>
                                            <button class="btn btn-outline-info" id="btn_filtrar_rifas"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Aplicar filtros">
                                                <i class="ri-filter-line me-1"></i>Filtrar
                                            </button>
                                            <button class="btn btn-outline-warning" id="btn_recargar_rifas"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Recargar listado y limpiar filtros">
                                                <i class="ri-refresh-line me-1"></i>
                                            </button>
                                        </div>

                                        <div class="ms-auto d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-success" id="btn_exportar_10" data-print="10"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Generar cartillas en formato de 10 por página">
                                                <i class="ri-printer-line me-1"></i>Cartillas x10
                                            </button>
                                            <button type="button" class="btn btn-success" id="btn_exportar_20" data-print="20"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Generar cartillas en formato de 20 por página">
                                                <i class="ri-printer-line me-1"></i>Cartillas x20
                                            </button>
                                            <button type="button" class="btn btn-primary" id="btn_nueva_rifa"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Registrar nueva rifa o sorteo">
                                                <i class="ri-add-line me-1"></i>Nueva Rifa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tabla_rifas" class="table table-striped table-hover align-middle table-nowrap mb-0"
                                            style="width:100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Acciones</th>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Premio principal</th>
                                                    <th>Precio ticket</th>
                                                    <th>N° totales</th>
                                                     <th>Estado</th>
                                                    <th>Fecha sorteo</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php require_once __DIR__ . '/form.php'; ?>


                </div>
            </div>

            <?php require_once __DIR__ . '/../components/footer.php'; ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/../components/js.php'; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/rifas/rifas.js"></script>
</body>

</html>

