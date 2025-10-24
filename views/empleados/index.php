<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Lista de Empleados | Control de Asistencia CAFED</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Control de Asistencia CAFED" name="Gestión de empleados" />
    <meta content="Cafed" name="Team Otic Cafed" />
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
                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Gestión de Empleados</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Empleados</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Main Content -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-user-line me-2"></i>Filtros y Acciones
                                    </h5>

                                    <!-- Contenedor responsive para filtros y botones -->
                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <!-- Grupo del selector de fechas - ancho completo en móvil -->
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3">
                                            <div class="input-group" style="max-width: 220px; min-width: 180px;">
                                                <input type="text" id="fecha_rango" class="form-control"
                                                    placeholder="Seleccionar fechas"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Seleccionar rango de fechas para filtrar" />
                                            </div>
                                            <!-- Combo para filtrar por estado -->
                                            <div class="input-group" style="max-width: 200px; min-width: 160px;">
                                                <select id="estado_filtro" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar por estado del empleado">
                                                    <option value="">Todos los estados</option>
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                            </div>

                                            <!-- Combo para filtrar por unidad organizacional -->
                                            <div class="input-group" style="max-width: 200px; min-width: 160px;">
                                                <select id="unidad_filtro" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar por unidad organizacional">
                                                    <option value="">Todas las unidades</option>
                                                    <option value="1">Gerencia General</option>
                                                    <option value="2">Recursos Humanos</option>
                                                    <option value="3">Contabilidad</option>
                                                    <option value="4">Ventas</option>
                                                    <option value="5">Marketing</option>
                                                    <option value="6">Tecnología</option>
                                                    <option value="7">Operaciones</option>
                                                </select>
                                            </div>

                                            <button type="button" id="btn_filtrar" class="btn btn-outline-info"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Filtrar empleados por fecha, estado y unidad">
                                                <i class="ri-filter-line me-1"></i>Filtrar
                                            </button>

                                            <button type="button" id="btn_recargar" class="btn btn-outline-warning"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Recargar tabla y limpiar filtros">
                                                <i class="ri-refresh-line me-1"></i>
                                            </button>
                                        </div>

                                        <!-- Botones de acción - alineados correctamente en móvil -->
                                        <div class="ms-auto ms-md-0 d-flex gap-2">
                                            <button type="button" class="btn btn-success" id="btn_nuevo_empleado"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Registrar nuevo empleado">
                                                <i class="ri-add-line align-bottom me-1"></i>Nuevo Empleado
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table_empleados"
                                            class="table table-hover align-middle table-nowrap mb-0" style="width:100% !important;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" class="text-center">Acciones</th>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">N° Documento</th>
                                                    <th scope="col">Nombres Completos</th>
                                                    <th scope="col">Email</th>
                                                    <th scope="col">Cargo</th>
                                                    <th scope="col">Unidad Organizacional</th>
                                                    <th scope="col">Fecha Ingreso</th>
                                                    <th scope="col">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Los datos se cargarán dinámicamente via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once __DIR__ . '/../components/footer.php' ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/../components/js.php' ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/empleados/list_empleados.js"></script>
</body>

</html>
