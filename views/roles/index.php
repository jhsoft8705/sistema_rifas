<?php
require_once __DIR__ . "/../../config/Enrutamiento.php";
?>
<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Roles | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de roles del sistema" name="description" />
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
                                <h4 class="mb-sm-0">Roles</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/admin-dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Roles</li>
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
                                        <i class="ri-shield-user-line me-2"></i>Gestión de Roles
                                    </h5>
                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="ms-auto ms-md-0 d-flex gap-2">
                                            <button type="button" class="btn btn-primary" id="btn_nuevo_rol"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;">
                                                <i class="ri-add-line align-bottom me-1"></i>Nuevo Rol
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle table-nowrap mb-0" id="tabla_roles">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Acciones</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Nivel Acceso</th>
                                                    <th>Usuarios</th>
                                                    <th>Permisos</th>
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

    <!-- Modal para crear/editar rol -->
    <div class="modal fade" id="modal_rol" tabindex="-1" aria-labelledby="modal_rol_label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_rol_label">Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_rol">
                        <input type="hidden" id="rol_id" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rol_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="rol_nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rol_nivel_acceso" class="form-label">Nivel de Acceso <span class="text-danger">*</span></label>
                                <select class="form-select" id="rol_nivel_acceso" name="nivel_acceso" required>
                                    <option value="1">Básico</option>
                                    <option value="2">Intermedio</option>
                                    <option value="3">Avanzado</option>
                                    <option value="4">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="rol_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="rol_descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="rol_estado" class="form-label">Estado</label>
                                <select class="form-select" id="rol_estado" name="estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_rol">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para asignar permisos -->
    <div class="modal fade" id="modal_permisos_rol" tabindex="-1" aria-labelledby="modal_permisos_rol_label" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_permisos_rol_label">Asignar Permisos al Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="contenedor_permisos" class="row">
                        <!-- Los permisos se cargarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_permisos_rol">Guardar Permisos</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . "/../components/js.php"; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/roles/roles.js"></script>
</body>

</html>
