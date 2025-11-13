<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Tickets | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de tickets de rifas" name="description" />
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
                                <h4 class="mb-sm-0">Gestión de Tickets</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Tickets</li>
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
                                        <i class="ri-ticket-line me-2"></i>Filtros y Acciones
                                    </h5>

                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3 align-items-center">
                                            <div class="w-auto" style="max-width: 240px; min-width: 200px;">
                                                <select id="filtro_sede_ticket" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar tickets por sede">
                                                    <option value="">Todas las sedes</option>
                                                </select>
                                            </div>
                                            <div class="w-auto" style="max-width: 240px; min-width: 200px;">
                                                <select id="filtro_rifa_ticket" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar tickets por rifa">
                                                    <option value="">Todas las rifas</option>
                                                </select>
                                            </div>
                                            <div class="w-auto" style="max-width: 220px; min-width: 180px;">
                                                <select id="filtro_estado_ticket" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar tickets por estado">
                                                    <option value="">Todos los estados</option>
                                                    <option value="PENDIENTE_PAGO">Pendiente de pago</option>
                                                    <option value="PAGO_SUBIDO">Pago subido</option>
                                                    <option value="VALIDANDO">Validando</option>
                                                    <option value="APROBADO">Aprobado</option>
                                                    <option value="RECHAZADO">Rechazado</option>
                                                    <option value="PARTICIPANDO">Participando</option>
                                                    <option value="GANADOR">Ganador</option>
                                                    <option value="EXPIRADO">Expirado</option>
                                                </select>
                                            </div>
                                            <button class="btn btn-outline-info" id="btn_filtrar_tickets"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Aplicar filtros">
                                                <i class="ri-filter-line me-1"></i>Filtrar
                                            </button>
                                            <button class="btn btn-outline-warning" id="btn_recargar_tickets"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Recargar listado y limpiar filtros">
                                                <i class="ri-refresh-line me-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tabla_tickets" class="table table-striped table-hover align-middle table-nowrap mb-0"
                                            style="width:100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Acciones</th>
                                                    <th>Código Ticket</th>
                                                    <th>Rifa</th>
                                                    <th>Participante</th>
                                                    <th>Documento</th>
                                                    <th>Número Boleto</th>
                                                    <th>Precio</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Compra</th>
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

            <?php require_once __DIR__ . '/../components/footer.php'; ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/../components/js.php'; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/tickets/tickets.js"></script>
</body>

</html>

