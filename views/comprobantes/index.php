<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Validación de Comprobantes | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Validación de comprobantes de pago" name="description" />
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
                                <h4 class="mb-sm-0">Validación de Comprobantes de Pago</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Comprobantes</li>
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
                                        <i class="ri-file-check-line me-2"></i>Filtros y Acciones
                                    </h5>

                                    <div class="d-flex flex-wrap w-100 gap-3 mt-3">
                                        <div class="d-flex flex-wrap flex-grow-1 gap-3 align-items-center">
                                            <div class="w-auto" style="max-width: 240px; min-width: 200px;">
                                                <select id="filtro_sede_comprobante" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar comprobantes por sede">
                                                    <option value="">Todas las sedes</option>
                                                </select>
                                            </div>
                                            <div class="w-auto" style="max-width: 220px; min-width: 180px;">
                                                <select id="filtro_estado_comprobante" class="form-select"
                                                    style="min-height: 40px; font-size: 0.9rem;"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="Filtrar comprobantes por estado">
                                                    <option value="">Todos los estados</option>
                                                    <option value="PENDIENTE">Pendiente</option>
                                                    <option value="VALIDANDO">Validando</option>
                                                    <option value="APROBADO">Aprobado</option>
                                                    <option value="RECHAZADO">Rechazado</option>
                                                </select>
                                            </div>
                                            <button class="btn btn-outline-info" id="btn_filtrar_comprobantes"
                                                style="min-height: 40px; font-size: 0.9rem; padding: 0.5rem 1rem;"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Aplicar filtros">
                                                <i class="ri-filter-line me-1"></i>Filtrar
                                            </button>
                                            <button class="btn btn-outline-warning" id="btn_recargar_comprobantes"
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
                                        <table id="tabla_comprobantes" class="table table-striped table-hover align-middle table-nowrap mb-0"
                                            style="width:100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">Acciones</th>
                                                    <th>Código Ticket</th>
                                                    <th>Participante</th>
                                                    <th>Rifa</th>
                                                    <th>Monto</th>
                                                    <th>N° Operación</th>
                                                    <th>Fecha Pago</th>
                                                    <th>Días Esperando</th>
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

            <?php require_once __DIR__ . '/../components/footer.php'; ?>
        </div>
    </div>

    <!-- Modal para validar comprobante -->
    <div class="modal fade" id="modal_validar_comprobante" tabindex="-1" aria-labelledby="modal_validar_comprobante_label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_validar_comprobante_label">
                        <i class="ri-file-check-line me-2"></i>Validar Comprobante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="comprobante_id_validar">
                    <input type="hidden" id="sede_id_comprobante_validar">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Información del Ticket:</strong>
                                <div id="info_ticket_comprobante"></div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Comprobante de Pago</label>
                            <div id="preview_comprobante" class="text-center border rounded p-3 mb-3" style="min-height: 200px;">
                                <p class="text-muted">Cargando imagen...</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Monto</label>
                            <input type="text" class="form-control" id="monto_comprobante" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">N° Operación</label>
                            <input type="text" class="form-control" id="numero_operacion_comprobante" readonly>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Acción</label>
                            <select class="form-select" id="accion_comprobante" required>
                                <option value="">Seleccione una acción</option>
                                <option value="APROBADO">Aprobar comprobante</option>
                                <option value="RECHAZADO">Rechazar comprobante</option>
                            </select>
                            <div class="invalid-feedback">Seleccione una acción</div>
                        </div>
                        
                        <div class="col-12 d-none" id="contenedor_motivo_rechazo">
                            <label class="form-label">Motivo de rechazo <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="motivo_rechazo_comprobante" rows="3" 
                                placeholder="Indique el motivo por el cual se rechaza el comprobante"></textarea>
                            <div class="invalid-feedback">El motivo de rechazo es obligatorio</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="btn_guardar_validacion">
                        <i class="ri-save-line me-1"></i>Guardar Validación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../components/js.php'; ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/comprobantes/comprobantes.js"></script>
</body>

</html>

