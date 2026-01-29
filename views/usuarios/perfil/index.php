<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">
<?php
require_once __DIR__ . "/../../../config/Enrutamiento.php";
?>
<head>
    <meta charset="utf-8" />
    <title>Mi Perfil | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Gestión de perfil de usuario" name="description" />
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
                                <h4 class="mb-sm-0">Mi Perfil</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/views/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Mi Perfil</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_datos" role="tab">
                                                <i class="ri-user-line me-1 align-bottom"></i> Mis Datos
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tab_password" role="tab">
                                                <i class="ri-lock-password-line me-1 align-bottom"></i> Cambiar Contraseña
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <!-- Tab: Mis Datos -->
                                        <div class="tab-pane active" id="tab_datos" role="tabpanel">
                                            <form id="form_perfil_datos" novalidate>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="perfil_username" class="form-label">Usuario (login)</label>
                                                        <input type="text" class="form-control" id="perfil_username" 
                                                            readonly disabled>
                                                        <small class="text-muted">El nombre de usuario no se puede modificar</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="perfil_email" class="form-label">Email <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" id="perfil_email" 
                                                            name="email" placeholder="correo@ejemplo.com" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="perfil_primer_nombre" class="form-label">Nombres <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="perfil_primer_nombre" 
                                                            name="primer_nombre" placeholder="Nombres" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="perfil_apellido_paterno" class="form-label">Apellido Paterno <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="perfil_apellido_paterno" 
                                                            name="apellido_paterno" placeholder="Apellido paterno" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="perfil_apellido_materno" class="form-label">Apellido Materno</label>
                                                        <input type="text" class="form-control" id="perfil_apellido_materno" 
                                                            name="apellido_materno" placeholder="Apellido materno">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="perfil_telefono" class="form-label">Teléfono</label>
                                                        <input type="text" class="form-control" id="perfil_telefono" 
                                                            name="telefono" placeholder="+51 999 999 999">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Rol</label>
                                                        <input type="text" class="form-control" id="perfil_rol" readonly disabled>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Sede</label>
                                                        <input type="text" class="form-control" id="perfil_sede" readonly disabled>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Último Acceso</label>
                                                        <input type="text" class="form-control" id="perfil_ultimo_acceso" readonly disabled>
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn-primary" id="btn_guardar_datos">
                                                        <i class="ri-save-line me-1"></i>Guardar Cambios
                                                    </button>
                                                    <button type="button" class="btn btn-light ms-2" id="btn_cancelar_datos">
                                                        <i class="ri-close-line me-1"></i>Cancelar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Tab: Cambiar Contraseña -->
                                        <div class="tab-pane" id="tab_password" role="tabpanel">
                                            <form id="form_perfil_password" novalidate>
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <label for="perfil_password_actual" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="perfil_password_actual" 
                                                            name="password_actual" placeholder="Ingrese su contraseña actual" required autocomplete="current-password">
                                                        <div class="invalid-feedback"></div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="perfil_password_nueva" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="perfil_password_nueva" 
                                                            name="password_nueva" placeholder="Mín. 6 caracteres" required autocomplete="new-password">
                                                        <div class="invalid-feedback"></div>
                                                        <small class="text-muted">Mínimo 6 caracteres</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="perfil_password_nueva_confirmar" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="perfil_password_nueva_confirmar" 
                                                            name="password_nueva_confirmar" placeholder="Confirme la nueva contraseña" required autocomplete="new-password">
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>

                                                <div class="alert alert-info mt-3" role="alert">
                                                    <i class="ri-information-line me-2"></i>
                                                    <strong>Recomendaciones de seguridad:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Use al menos 6 caracteres</li>
                                                        <li>Combine letras, números y símbolos</li>
                                                        <li>No comparta su contraseña con nadie</li>
                                                        <li>Cambie su contraseña periódicamente</li>
                                                    </ul>
                                                </div>

                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn-primary" id="btn_guardar_password">
                                                        <i class="ri-lock-password-line me-1"></i>Cambiar Contraseña
                                                    </button>
                                                    <button type="button" class="btn btn-light ms-2" id="btn_cancelar_password">
                                                        <i class="ri-close-line me-1"></i>Cancelar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once __DIR__ . "/../../components/footer.php"; ?>
        </div>
    </div>

    <?php require_once __DIR__ . "/../../components/js.php"; ?>

    <!-- Perfil JS -->
    <script src="<?= Enrutamiento::dominio() ?>/views/usuarios/perfil/perfil.js"></script>

    <!-- Perfil Init -->
    <script>
        // Proteger ruta - Requiere autenticación
        if (!Auth.isAuthenticated()) {
            window.location.href = window.BASE_URL;
        }

        console.log('✓ Perfil cargado correctamente');
    </script>

</body>
</html>
