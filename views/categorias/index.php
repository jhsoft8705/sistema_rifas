<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Categorías de Premios | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de categorías para premios" name="description" />
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
                                <h4 class="mb-sm-0">Categorías de Premios</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Categorías</li>
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
                                        <i class="ri-price-tag-3-line me-2"></i>Filtros y Acciones
                                    </h5>

                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3">
                                            <div class="w-auto" style="max-width: 220px; min-width: 180px;">
                                                <select id="filtro_sede" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar categorías por sede">
                                                    <option value="">Seleccionar sede</option>
                                                </select>
                                            </div>

                                            <div class="w-auto" style="max-width: 200px; min-width: 160px;">
                                                <select id="filtro_estado" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar categorías por estado">
                                                    <option value="">Todos</option>
                                                    <option value="1">Activas</option>
                                                    <option value="0">Inactivas</option>
                                                </select>
                                            </div>

                                            <button class="btn btn-outline-info" id="btn_filtrar_categorias"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Aplicar filtros">
                                                <i class="ri-filter-line me-1"></i>Filtrar
                                            </button>

                                            <button class="btn btn-outline-warning" id="btn_recargar_categorias"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Recargar y limpiar filtros">
                                                <i class="ri-refresh-line me-1"></i>
                                            </button>
                                        </div>

                                        <div class="ms-auto ms-md-0 d-flex gap-2">
                                            <button type="button" class="btn btn-primary" id="btn_nueva_categoria"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Registrar nueva categoría">
                                                <i class="ri-add-line align-bottom me-1"></i>Nueva Categoría
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-nowrap mb-0" id="tabla_categorias">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Acciones</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Icono</th>
                                                    <th>Color</th>
                                                    <th>Orden</th>
                                                    <th>Estado</th>
                                                    <th>Creado</th>
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
    <script src="<?= Enrutamiento::dominio() ?>/views/categorias/categorias.js"></script>
</body>

</html>


