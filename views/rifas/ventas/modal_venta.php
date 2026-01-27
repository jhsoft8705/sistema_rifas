<!-- Modal para Registrar Venta Administrativa -->
<div class="modal fade" id="modal_registrar_venta" tabindex="-1" aria-labelledby="modal_registrar_venta_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="modal_registrar_venta_label">
                    <i class="ri-shopping-cart-line me-2 text-success"></i>
                    <span id="modal_titulo_rifa_venta">Registrar Venta</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="form_registrar_venta" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="venta_rifa_id" name="rifa_id">
                    
                    <div class="row">
                        <!-- Columna Izquierda - Formulario -->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Navigation Tabs -->
                                    <div class="step-arrow-nav mt-n3 mx-n3 mb-3">
                                        <ul class="nav nav-pills nav-justified custom-nav" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link fs-15 p-3 active" id="venta-personal-tab" 
                                                        data-bs-toggle="pill" data-bs-target="#venta-personal" 
                                                        type="button" role="tab" aria-controls="venta-personal" 
                                                        aria-selected="true">
                                                    <i class="ri-user-2-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2"></i> 
                                                    <span>Datos del Cliente</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link fs-15 p-3" id="venta-orden-tab" 
                                                        data-bs-toggle="pill" data-bs-target="#venta-orden" 
                                                        type="button" role="tab" aria-controls="venta-orden" 
                                                        aria-selected="false" disabled>
                                                    <i class="ri-shopping-cart-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2"></i> 
                                                    <span>Números</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link fs-15 p-3" id="venta-confirmar-tab" 
                                                        data-bs-toggle="pill" data-bs-target="#venta-confirmar" 
                                                        type="button" role="tab" aria-controls="venta-confirmar" 
                                                        aria-selected="false" disabled>
                                                    <i class="ri-checkbox-circle-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2"></i> 
                                                    <span>Confirmar</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Tab Content -->
                                    <div class="tab-content">
                                        
                                        <!-- Tab 1: Datos del Cliente -->
                                        <div class="tab-pane fade show active" id="venta-personal" role="tabpanel">
                                            <div>
                                                <h5 class="mb-1">Datos del Cliente</h5>
                                                <p class="text-muted mb-4">Ingresa los datos del participante</p>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_nombres" class="form-label">
                                                            Nombres <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="venta_nombres" 
                                                               name="nombres" placeholder="Ingrese nombres" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_apellidos" class="form-label">
                                                            Apellidos <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="venta_apellidos" 
                                                               name="apellidos" placeholder="Ingrese apellidos" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_email" class="form-label">
                                                            Correo Electrónico
                                                        </label>
                                                        <input type="email" class="form-control" id="venta_email" 
                                                               name="email" placeholder="correo@ejemplo.com">
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_telefono" class="form-label">
                                                            Teléfono / WhatsApp <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="tel" class="form-control" id="venta_telefono" 
                                                               name="telefono" placeholder="+51 999 999 999" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_tipo_documento" class="form-label">
                                                            Tipo de Documento <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-select" id="venta_tipo_documento" name="tipo_documento" required>
                                                            <option value="">Seleccionar...</option>
                                                            <option value="DNI">DNI</option>
                                                            <option value="CE">Carnet de Extranjería</option>
                                                            <option value="PASAPORTE">Pasaporte</option>
                                                            <option value="RUC">RUC</option>
                                                        </select>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_numero_documento" class="form-label">
                                                            Número de Documento <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="venta_numero_documento" 
                                                               name="numero_documento" placeholder="Ej: 12345678" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_ciudad" class="form-label">
                                                            Ciudad
                                                        </label>
                                                        <input type="text" class="form-control" id="venta_ciudad" 
                                                               name="ciudad" placeholder="Ej: Lima">
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="venta_direccion" class="form-label">
                                                            Dirección
                                                        </label>
                                                        <input type="text" class="form-control" id="venta_direccion" 
                                                               name="direccion" placeholder="Dirección completa">
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex align-items-start gap-3 mt-3">
                                                <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                        data-nexttab="venta-orden-tab" id="btn_continuar_venta_personal">
                                                    <i class="ri-shopping-cart-line label-icon align-middle fs-16 ms-2"></i>
                                                    Continuar a Números
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Tab 2: Selección de Números -->
                                        <div class="tab-pane fade" id="venta-orden" role="tabpanel">
                                            <div>
                                                <h5 class="mb-1">Selección de Números</h5>
                                                <p class="text-muted mb-4">Selecciona la cantidad y números de tickets</p>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="mb-4">
                                                        <label for="venta_cantidad_tickets" class="form-label">
                                                            ¿Cuántos tickets? <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group input-group-lg">
                                                            <button class="btn btn-outline-secondary" type="button" id="venta_btn_menos">
                                                                <i class="ri-subtract-line"></i>
                                                            </button>
                                                            <input type="number" class="form-control text-center fs-20 fw-semibold" 
                                                                   id="venta_cantidad_tickets" name="cantidad_tickets" value="1" min="1" max="999">
                                                            <button class="btn btn-outline-secondary" type="button" id="venta_btn_mas">
                                                                <i class="ri-add-line"></i>
                                                            </button>
                                                        </div>
                                                        <small class="text-muted d-block mt-2">
                                                            <i class="ri-information-line"></i> Tickets disponibles: <span id="venta_tickets_disponibles">0</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Sistema de Selección de Números -->
                                            <div class="card border border-success mb-4">
                                                <div class="card-body">
                                                    <h6 class="mb-3">
                                                        <i class="ri-hashtag text-success me-1"></i> Seleccionar Números
                                                    </h6>
                                                    
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <div class="card bg-light border h-100">
                                                                <div class="card-body text-center p-3">
                                                                    <div class="avatar-sm mx-auto mb-2">
                                                                        <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                            <i class="ri-hashtag fs-18"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="mb-2">Selección Manual</h6>
                                                                    <p class="text-muted small mb-3">Elige números específicos</p>
                                                                    <button type="button" class="btn btn-primary btn-sm" onclick="mostrarGridNumerosVenta()">
                                                                        <i class="ri-search-line me-1"></i> Ver Números Disponibles
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-6">
                                                            <div class="card bg-light border h-100">
                                                                <div class="card-body text-center p-3">
                                                                    <div class="avatar-sm mx-auto mb-2">
                                                                        <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                                                            <i class="ri-shuffle-line fs-18"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="mb-2">Asignación Aleatoria</h6>
                                                                    <p class="text-muted small mb-3">Asignar automáticamente</p>
                                                                    <button type="button" class="btn btn-success btn-sm" onclick="asignarNumerosAleatoriosVenta()">
                                                                        <i class="ri-refresh-line me-1"></i> Asignar Aleatorios
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Números Seleccionados -->
                                                    <div id="venta_numero_seleccionado_display" style="display: none;">
                                                        <div class="alert alert-warning border-0 mb-0">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="ri-ticket-2-line fs-20 me-2"></i>
                                                                    <div>
                                                                        <strong>Números seleccionados:</strong>
                                                                        <span class="badge bg-info ms-2" id="venta_contador_numeros">0/0</span>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-sm btn-light" onclick="cancelarTodasLasSeleccionesVenta()">
                                                                    <i class="ri-close-line"></i> Limpiar
                                                                </button>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-2 mb-2" id="venta_lista_numeros_seleccionados">
                                                                <!-- Se llenará dinámicamente -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <input type="hidden" id="venta_numeros_reservados" name="numeros_reservados">
                                                    <input type="hidden" id="venta_numeros_formateados" name="numeros_formateados">
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button" class="btn btn-light btn-label previestab" 
                                                        data-previous="venta-personal-tab">
                                                    <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                    Volver
                                                </button>
                                                <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                        data-nexttab="venta-confirmar-tab" id="btn_continuar_venta_orden" disabled>
                                                    <i class="ri-checkbox-circle-line label-icon align-middle fs-16 ms-2"></i>
                                                    Continuar a Confirmar
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Tab 3: Confirmar Venta -->
                                        <div class="tab-pane fade" id="venta-confirmar" role="tabpanel">
                                            <div class="text-center py-4">
                                                <div class="mb-4">
                                                    <i class="ri-checkbox-circle-line text-success" style="font-size: 72px;"></i>
                                                </div>
                                                <h5 class="mb-2">Confirmar Venta</h5>
                                                <p class="text-muted">Verifica la información antes de confirmar</p>
                                            </div>
                                            
                                            <!-- Resumen -->
                                            <div class="card bg-light border-0 mb-4">
                                                <div class="card-body">
                                                    <h6 class="mb-3">Resumen de la Venta</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-borderless mb-0">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-muted">Rifa:</td>
                                                                    <td class="text-end fw-semibold" id="venta_resumen_rifa">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Cliente:</td>
                                                                    <td class="text-end fw-semibold" id="venta_resumen_cliente">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Documento:</td>
                                                                    <td class="text-end" id="venta_resumen_documento">-</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Teléfono:</td>
                                                                    <td class="text-end" id="venta_resumen_telefono">-</td>
                                                                </tr>
                                                                <tr id="venta_resumen_numero_row" style="display: none;">
                                                                    <td class="text-muted">Números:</td>
                                                                    <td class="text-end">
                                                                        <div class="d-flex flex-wrap gap-1 justify-content-end" id="venta_resumen_numeros">
                                                                            <!-- Se llenará dinámicamente -->
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Cantidad:</td>
                                                                    <td class="text-end fw-semibold" id="venta_resumen_cantidad">1</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-muted">Precio unitario:</td>
                                                                    <td class="text-end" id="venta_resumen_precio">S/. 0.00</td>
                                                                </tr>
                                                                <tr class="border-top">
                                                                    <td class="fw-semibold fs-15">Total:</td>
                                                                    <td class="text-end fw-bold text-success fs-18" id="venta_resumen_total">S/. 0.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Opciones de Pago -->
                                            <div class="mb-4">
                                                <label class="form-label">Estado del Pago <span class="text-danger">*</span></label>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-check form-check-card">
                                                            <input class="form-check-input" type="radio" name="estado_pago" id="pago_pagado" value="PAGADO" checked>
                                                            <label class="form-check-label w-100" for="pago_pagado">
                                                                <div class="card border border-success">
                                                                    <div class="card-body p-3">
                                                                        <i class="ri-checkbox-circle-line text-success fs-20 mb-2"></i>
                                                                        <h6 class="mb-1">Pagado</h6>
                                                                        <small class="text-muted">Marcar como pagado y aprobado</small>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check form-check-card">
                                                            <input class="form-check-input" type="radio" name="estado_pago" id="pago_pendiente" value="PENDIENTE_PAGO">
                                                            <label class="form-check-label w-100" for="pago_pendiente">
                                                                <div class="card border border-warning">
                                                                    <div class="card-body p-3">
                                                                        <i class="ri-time-line text-warning fs-20 mb-2"></i>
                                                                        <h6 class="mb-1">Pendiente</h6>
                                                                        <small class="text-muted">Marcar como pendiente de pago</small>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex align-items-start gap-3 mt-4">
                                                <button type="button" class="btn btn-light btn-label previestab" 
                                                        data-previous="venta-orden-tab">
                                                    <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                    Volver
                                                </button>
                                                <button type="submit" class="btn btn-success btn-label right ms-auto" id="btn_confirmar_venta">
                                                    <i class="ri-shopping-bag-line label-icon align-middle fs-16 ms-2"></i>
                                                    Confirmar Venta
                                                </button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna Derecha - Resumen -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header bg-success-subtle border-0">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-shopping-bag-line me-2"></i>Resumen
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless align-middle mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted">Precio por Ticket:</td>
                                                    <td class="text-end fw-semibold">S/. <span id="venta_precio_ticket">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Cantidad:</td>
                                                    <td class="text-end fw-semibold"><span id="venta_cantidad_display">1</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Disponibles:</td>
                                                    <td class="text-end">
                                                        <span class="badge bg-primary-subtle text-primary">
                                                            <span id="venta_tickets_disponibles_resumen">0</span>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr class="table-active">
                                                    <th class="fs-15">Total:</th>
                                                    <th class="text-end">
                                                        <span class="fw-semibold text-success fs-18">
                                                            S/. <span id="venta_total_pagar">0.00</span>
                                                        </span>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Seleccionar Números (Venta) -->
<div class="modal fade" id="modal_seleccionar_numero_venta" tabindex="-1" aria-labelledby="modal_seleccionar_numero_venta_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle border-0">
                <h5 class="modal-title" id="modal_seleccionar_numero_venta_label">
                    <i class="ri-hashtag me-2"></i>Selecciona Números Disponibles
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-search-line"></i></span>
                            <input type="text" class="form-control" id="venta_buscar_numero" 
                                   placeholder="Buscar número...">
                            <button class="btn btn-primary" type="button" onclick="buscarNumeroVenta()">
                                Buscar
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card border">
                    <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
                        <div id="venta_grid_numeros_disponibles" class="row g-2">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver/Imprimir Comprobante -->
<div class="modal fade" id="modal_comprobante" tabindex="-1" aria-labelledby="modal_comprobante_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_comprobante_label">
                    <i class="ri-file-paper-line me-2"></i>Comprobante de Compra
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Contenido para impresión -->
                <div id="contenido_comprobante_imprimir" class="comprobante-print">
                    <div class="row g-2">
                        <!-- Encabezado del comprobante -->
                        <div class="col-12">
                            <div class="text-center mb-2 pb-2 border-bottom">
                                <h5 class="mb-1">COMPROBANTE DE COMPRA</h5>
                                <p class="text-muted mb-0 small">Sistema de Rifas</p>
                            </div>
                        </div>

                        <!-- Información del ticket -->
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Código de Ticket</label>
                            <div class="form-control-plaintext fw-bold text-primary" id="comprobante_codigo">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Fecha</label>
                            <div class="form-control-plaintext" id="comprobante_fecha">-</div>
                        </div>

                        <!-- Datos del cliente -->
                        <div class="col-12">
                            <h6 class="mb-2 mt-1">
                                <i class="ri-user-line me-2"></i>Datos del Cliente
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Cliente</label>
                            <div class="form-control-plaintext fw-semibold" id="comprobante_cliente">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Documento</label>
                            <div class="form-control-plaintext" id="comprobante_documento">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Teléfono</label>
                            <div class="form-control-plaintext" id="comprobante_telefono">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <div class="form-control-plaintext" id="comprobante_email">-</div>
                        </div>

                        <!-- Detalle de la compra -->
                        <div class="col-12">
                            <h6 class="mb-2 mt-1">
                                <i class="ri-shopping-cart-line me-2"></i>Detalle de la Compra
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Rifa</label>
                            <div class="form-control-plaintext fw-semibold" id="comprobante_rifa">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Cantidad</label>
                            <div class="form-control-plaintext" id="comprobante_cantidad">-</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Números</label>
                            <div class="form-control-plaintext" id="comprobante_numeros">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Precio Unitario</label>
                            <div class="form-control-plaintext" id="comprobante_precio_unitario">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Total Pagado</label>
                            <div class="form-control-plaintext fw-bold text-success fs-5" id="comprobante_total">-</div>
                        </div>

                        <!-- Información adicional -->
                        <div class="col-12">
                            <h6 class="mb-2 mt-1">
                                <i class="ri-information-line me-2"></i>Información Adicional
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Estado</label>
                            <div class="form-control-plaintext" id="comprobante_estado">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Sede</label>
                            <div class="form-control-plaintext" id="comprobante_sede">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="copiarComprobante()">
                    <i class="ri-file-copy-line me-1"></i>Copiar
                </button>
                <button type="button" class="btn btn-outline-info" onclick="compartirComprobante()">
                    <i class="ri-share-line me-1"></i>Compartir
                </button>
                <button type="button" class="btn btn-success" onclick="imprimirComprobantePDF()">
                    <i class="ri-printer-line me-1"></i>Imprimir PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Aprobar/Rechazar Venta -->
<div class="modal fade" id="modal_aprobar_venta" tabindex="-1" aria-labelledby="modal_aprobar_venta_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_aprobar_venta_label">
                    <i class="ri-checkbox-circle-line me-2"></i>Aprobar/Rechazar Pago
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_aprobar_venta" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="venta_ticket_id_aprobar" name="ticket_id">
                    <input type="hidden" id="venta_sede_id_aprobar" name="sede_id">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong><i class="ri-ticket-line me-1"></i>Información del Ticket:</strong>
                                <div id="venta_info_ticket_aprobar" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Comprobante de Pago</label>
                            <div id="venta_preview_comprobante_aprobar" class="text-center border rounded p-3 mb-3" style="min-height: 200px;">
                                <p class="text-muted">Cargando comprobante...</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Precio Pagado</label>
                            <input type="text" class="form-control" id="venta_precio_aprobar" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Compra</label>
                            <input type="text" class="form-control" id="venta_fecha_compra_aprobar" readonly>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Acción <span class="text-danger">*</span></label>
                            <select class="form-select" id="venta_accion_aprobar" name="accion" required>
                                <option value="">Seleccione una acción</option>
                                <option value="APROBADO">Aprobar pago</option>
                                <option value="RECHAZADO">Rechazar pago</option>
                            </select>
                            <div class="invalid-feedback">Seleccione una acción</div>
                        </div>
                        
                        <div class="col-12 d-none" id="venta_contenedor_motivo_rechazo">
                            <label class="form-label">Motivo de rechazo <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="venta_motivo_rechazo" name="motivo_rechazo" rows="3" 
                                placeholder="Indique el motivo por el cual se rechaza el pago"></textarea>
                            <div class="invalid-feedback">El motivo de rechazo es obligatorio</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_aprobacion_venta">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    .comprobante-print {
        padding: 20px;
    }
    .modal-footer {
        display: none;
    }
}
</style>
