<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Números de Rifa | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Visualización de números disponibles para rifa" name="description" />
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
                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Números de Rifa</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio() ?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio() ?>/admin-rifas">Rifas</a></li>
                                        <li class="breadcrumb-item active">Números</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la Rifa -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="info-rifa" id="info_rifa_container">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                            <div>
                                                <h3 class="mb-2" id="rifa_nombre">Cargando...</h3>
                                                <p class="mb-0 opacity-75" id="rifa_descripcion"></p>
                                            </div>
                                            <div class="text-end">
                                                <div class="mb-2">
                                                    <small class="opacity-75">Precio por ticket</small>
                                                    <div class="h4 mb-0" id="rifa_precio">-</div>
                                                </div>
                                                <div>
                                                    <small class="opacity-75">Sorteo</small>
                                                    <div id="rifa_fecha_sorteo">-</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros y Estadísticas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-filter-line me-2"></i>Filtros y Estadísticas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="filtros-container">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label mb-2">Filtrar por estado</label>
                                                <select id="filtro_estado" class="form-select">
                                                    <option value="">Todos</option>
                                                    <option value="DISPONIBLE">Disponibles</option>
                                                    <option value="RESERVADO">Reservados</option>
                                                    <option value="VENDIDO">Vendidos</option>
                                                    <option value="BLOQUEADO">Bloqueados</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label mb-2">Buscar número</label>
                                                <input type="text" id="buscar_numero" class="form-control" placeholder="Ej: 001, 050">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <button class="btn btn-primary" id="btn_aplicar_filtros">
                                                        <i class="ri-filter-line me-1"></i>Aplicar Filtros
                                                    </button>
                                                    <button class="btn btn-outline-secondary" id="btn_limpiar_filtros">
                                                        <i class="ri-refresh-line me-1"></i>Limpiar
                                                    </button>
                                                    <button class="btn btn-success" id="btn_imprimir">
                                                        <i class="ri-printer-line me-1"></i>Imprimir
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body text-center p-2">
                                                        <div class="text-muted small">Total</div>
                                                        <div class="h5 mb-0" id="stat_total">0</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-success bg-opacity-10">
                                                    <div class="card-body text-center p-2">
                                                        <div class="text-success small">Disponibles</div>
                                                        <div class="h5 mb-0 text-success" id="stat_disponibles">0</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-warning bg-opacity-10">
                                                    <div class="card-body text-center p-2">
                                                        <div class="text-warning small">Reservados</div>
                                                        <div class="h5 mb-0 text-warning" id="stat_reservados">0</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="card border-0 bg-primary bg-opacity-10">
                                                    <div class="card-body text-center p-2">
                                                        <div class="text-primary small">Vendidos</div>
                                                        <div class="h5 mb-0 text-primary" id="stat_vendidos">0</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid de Números -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-grid-line me-2"></i>Números Disponibles
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="loading_numeros" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="mt-3 text-muted">Cargando números...</p>
                                    </div>
                                    <div id="error_numeros" class="alert alert-danger d-none" role="alert">
                                        <i class="ri-error-warning-line me-2"></i>
                                        <span id="error_mensaje"></span>
                                    </div>
                                    <div id="grid_numeros" class="grid-numeros d-none"></div>
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
    <script>
        // Configurar BASE_URL y API_BASE_URL
        window.BASE_URL = '<?= Enrutamiento::dominio() ?>';
        window.API_BASE_URL = window.BASE_URL + '/api';
    </script>
    <script src="<?= Enrutamiento::dominio() ?>/helpers/Utils.js"></script>
    <script src="<?= Enrutamiento::dominio() ?>/views/rifas/numeros/numeros.js"></script>
</body>

</html>
