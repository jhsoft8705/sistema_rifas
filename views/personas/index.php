<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Personas | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de personas/clientes para rifas" name="description" />
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
                                <h4 class="mb-sm-0">Gestión de Personas</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Personas</li>
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
                                        <i class="ri-user-line me-2"></i>Filtros y Acciones
                                    </h5>

                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3">
                                            <button class="btn btn-outline-warning" id="btn_recargar_personas"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Recargar listado">
                                                <i class="ri-refresh-line me-1"></i>
                                            </button>
                                        </div>

                                        <div class="ms-auto ms-md-0 d-flex gap-2">
                                            <button type="button" class="btn btn-primary" id="btn_nuevo_persona"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Registrar nueva persona">
                                                <i class="ri-add-line align-bottom me-1"></i>Nueva Persona
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-nowrap mb-0" id="tabla_personas">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Acciones</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Documento</th>
                                                    <th>Email</th>
                                                    <th>Teléfono</th>
                                                    <th>Tickets</th>
                                                    <th>Rifas Participadas</th>
                                                    <th>Registrado</th>
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

    <?php require_once __DIR__ . '/form.php'; ?>
    <?php require_once __DIR__ . '/../components/js.php'; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/personas/personas.js"></script>
</body>

</html>
