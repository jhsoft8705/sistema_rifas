<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Cargos | Control de Asistencia CAFED</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Control de Asistencia CAFED" name="Gestión de cargos de empleados" />
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
                                <h4 class="mb-sm-0">Gestión de Cargos</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Cargos</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Acción Principal -->
                    <div class="row mb-1 ">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" id="btn_nuevo_cargo"
                                    style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="Crear nuevo cargo">
                                    <i class="ri-add-line align-bottom me-1 "></i> Nuevo Cargo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Filtros y Acciones -->
                    <div class="row mb-0 mt-2">
                        <div class="col-lg-12">
                            <div class="rounded mb-2 p-3" style="border: 1px solid #dee2e6;">
                                <h5 class="mb-3">
                                    <i class="ri-briefcase-line me-2"></i>Filtros y Acciones
                                </h5>

                                <!-- Contenedor responsive para filtros -->
                                <div class="d-flex flex-wrap w-100 gap-3">
                                        <!-- Grupo del selector de fechas - ancho completo en móvil -->
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3">
                                            <!-- Selector de fechas (comentado por ahora) -->
                                <div class="input-group" style="max-width: 220px; min-width: 180px;">
                                                <input type="text" id="fecha_rango" class="form-control"
                                                    placeholder="Seleccionar fechas"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" 
                                                    title="Seleccionar rango de fechas para filtrar" />
                                            </div> 

                                    <!-- Combo para filtrar por estado -->
                                     <div class="input-group" style="max-width: 200px; min-width: 160px;">
                                                <select id="estado_filtro" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar por estado del cargo">
                                                    <option value="">Todos los estados</option>
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                            </div>  
                                     <button type="button" id="btn_filtrar" class="btn btn-outline-info"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Filtrar cargos por fecha y estado">
                                                <i class="ri-filter-line me-1"></i>Filtrar
                                            </button> 

                                         

                                    <button type="button" id="btn_recargar" class="btn btn-outline-warning"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Recargar tabla y limpiar filtros">
                                                <i class="ri-refresh-line me-1"></i>
                                            </button>  

                                             <button type="button" id="btn_descargar_excel"
                                                class="btn btn-outline-success"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Descargar reporte en Excel">
                                                <i class="ri-file-excel-line me-1"></i>Excel
                                            </button>  
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Cargos -->
                    <div class="row ">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table_cargos"
                                            class="table table-hover align-middle table-nowrap mb-0" style="width:100% !important;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" class="text-center">Acciones</th>
                                                    <th scope="col">Nombre del Cargo</th>
                                                    <th scope="col">Descripción</th>
                                                    <th scope="col">Salario Base</th>
                                                    <th scope="col">Estado</th>
                                                    <th scope="col">Fecha Creación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Los datos se cargarán dinámicamente desde la API -->
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

    <?php require_once __DIR__ . '/form.php'; ?>
    <?php require_once __DIR__ . '/../components/js.php' ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/cargos/cargos.js"></script>
</body>

</html>