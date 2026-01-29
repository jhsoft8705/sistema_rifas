<?php
require_once __DIR__ . "/../../config/Enrutamiento.php";
?>
<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Permisos | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de permisos del sistema" name="description" />
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
                                <h4 class="mb-sm-0">Permisos</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/admin-dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Permisos</li>
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
                                        <i class="ri-shield-check-line me-2"></i>Gestión de Permisos
                                    </h5>
                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="ms-auto ms-md-0 d-flex gap-2">
                                            <button type="button" class="btn btn-primary" id="btn_nuevo_permiso"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;">
                                                <i class="ri-add-line align-bottom me-1"></i>Nuevo Permiso
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-nowrap mb-0" id="tabla_permisos">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Acciones</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Módulo</th>
                                                    <th>Acción</th>
                                                    <th>Estado</th>
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
            <?php require_once __DIR__ . "/../components/footer.php"; ?>
        </div>
    </div>

    <!-- Modal para crear/editar permiso -->
    <div class="modal fade" id="modal_permiso" tabindex="-1" aria-labelledby="modal_permiso_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_permiso_label">Nuevo Permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_permiso">
                        <input type="hidden" id="permiso_id" name="id">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="permiso_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="permiso_nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="permiso_modulo" class="form-label">Módulo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="permiso_modulo" name="modulo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="permiso_accion" class="form-label">Acción <span class="text-danger">*</span></label>
                                <select class="form-select" id="permiso_accion" name="accion" required>
                                    <option value="CREAR">CREAR</option>
                                    <option value="LEER">LEER</option>
                                    <option value="ACTUALIZAR">ACTUALIZAR</option>
                                    <option value="ELIMINAR">ELIMINAR</option>
                                    <option value="APROBAR">APROBAR</option>
                                    <option value="RECHAZAR">RECHAZAR</option>
                                    <option value="VER">VER</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="permiso_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="permiso_descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="permiso_estado" class="form-label">Estado</label>
                                <select class="form-select" id="permiso_estado" name="estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_permiso">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . "/../components/js.php"; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/permisos/permisos.js"></script>
</body>

</html>
