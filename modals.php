        <!-- Modal para Comprar Tickets - Estilo Checkout -->
        <div class="modal fade" id="modal_comprar_ticket" tabindex="-1" aria-labelledby="modal_comprar_ticket_label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title" id="modal_comprar_ticket_label">
                            <i class="ri-shopping-cart-line me-2 text-success"></i><span id="modal_titulo_rifa">Checkout - Comprar Tickets</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form id="form_comprar_ticket">
                        <div class="modal-body p-4">
                            <input type="hidden" id="rifa_id" name="rifa_id">
                            
                            <div class="row">
                                <!-- Columna Izquierda - Formulario con Tabs -->
                                <div class="col-xl-8">
                                    <div class="card">
                                        <div class="card-body">
                                            
                                            <!-- Navigation Tabs -->
                                            <div class="step-arrow-nav mt-n3 mx-n3 mb-3">
                                                <ul class="nav nav-pills nav-justified custom-nav" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3 active" id="pills-personal-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-personal" 
                                                                type="button" role="tab" aria-controls="pills-personal" 
                                                                aria-selected="true" data-position="0">
                                                            <i class="ri-user-2-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2"></i> 
                                                            <span>Información Personal</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3" id="pills-order-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-order" 
                                                                type="button" role="tab" aria-controls="pills-order" 
                                                                aria-selected="false" data-position="1" disabled>
                                                            <i class="ri-shopping-cart-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2"></i> 
                                                            <span>Tu Orden</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3" id="pills-payment-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-payment" 
                                                                type="button" role="tab" aria-controls="pills-payment" 
                                                                aria-selected="false" data-position="2" disabled>
                                                            <i class="ri-bank-card-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2"></i> 
                                                            <span>Pago</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3" id="pills-finish-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-finish" 
                                                                type="button" role="tab" aria-controls="pills-finish" 
                                                                aria-selected="false" data-position="3" disabled>
                                                            <i class="ri-checkbox-circle-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2"></i> 
                                                            <span>Confirmar</span>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Tab Content -->
                                            <div class="tab-content">
                                                
                                                <!-- Tab 1: Información Personal -->
                                                <div class="tab-pane fade show active" id="pills-personal" role="tabpanel">
                                                    <div>
                                                        <h5 class="mb-1">Información Personal</h5>
                                                        <p class="text-muted mb-4">Por favor, ingresa tus datos para participar</p>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="nombres" class="form-label">
                                                                    Nombres <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="nombres" 
                                                                       name="nombres" placeholder="Ingrese sus nombres" required>
                                                                <div class="text-danger small mt-1" id="nombres_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="apellidos" class="form-label">
                                                                    Apellidos <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="apellidos" 
                                                                       name="apellidos" placeholder="Ingrese sus apellidos" required>
                                                                <div class="text-danger small mt-1" id="apellidos_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="email_participante" class="form-label">
                                                                    Correo Electrónico <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="email" class="form-control" id="email_participante" 
                                                                       name="email_participante" placeholder="correo@ejemplo.com" required>
                                                                <div class="text-danger small mt-1" id="email_participante_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="telefono" class="form-label">
                                                                    Teléfono / WhatsApp <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="tel" class="form-control" id="telefono" 
                                                                       name="telefono" placeholder="+52 1 55 1234 5678">
                                                                <div class="text-danger small mt-1" id="telefono_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="tipo_documento" class="form-label">
                                                                    Tipo de Documento <span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-select" id="tipo_documento" name="tipo_documento">
                                                                    <option value="">Seleccionar...</option>
                                                                    <option value="DNI">DNI</option>
                                                                    <option value="CE">Carnet de Extranjería</option>
                                                                    <option value="PASAPORTE">Pasaporte</option>
                                                                    <option value="RUC">RUC</option>
                                                                </select>
                                                                <div class="text-danger small mt-1" id="tipo_documento_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="numero_documento" class="form-label">
                                                                    Número de Documento <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="numero_documento" 
                                                                       name="numero_documento" placeholder="Ej: 12345678">
                                                                <div class="text-danger small mt-1" id="numero_documento_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="estado" class="form-label">
                                                                    Estado/Provincia <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="estado" 
                                                                       name="estado" placeholder="Ej: CDMX, Jalisco..." required>
                                                                <div class="text-danger small mt-1" id="estado_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="ciudad" class="form-label">
                                                                    Ciudad
                                                                </label>
                                                                <input type="text" class="form-control" id="ciudad" 
                                                                       name="ciudad" placeholder="Ej: Ciudad de México, Guadalajara...">
                                                                <div class="text-danger small mt-1" id="ciudad_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="direccion_envio" class="form-label">
                                                            Dirección de Envío Completa <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control" id="direccion_envio" name="direccion_envio" 
                                                                  rows="3" placeholder="Calle, número, colonia, código postal, referencias..."></textarea>
                                                        <div class="text-danger small mt-1" id="direccion_envio_error" style="display: none;"></div>
                                                        <small class="text-muted">
                                                            <i class="ri-map-pin-line"></i> Esta dirección se usará para la entrega del premio en caso de resultar ganador(a)
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-3">
                                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                                data-nexttab="pills-order-tab" id="btn_continuar_personal" disabled>
                                                            <i class="ri-shopping-cart-line label-icon align-middle fs-16 ms-2"></i>
                                                            Continuar a tu Orden
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tab 2: Tu Orden -->
                                                <div class="tab-pane fade" id="pills-order" role="tabpanel">
                                                    <div>
                                                        <h5 class="mb-1">Información de tu Orden</h5>
                                                        <p class="text-muted mb-4">Selecciona la cantidad de tickets</p>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="mb-4">
                                                                <label for="cantidad_tickets" class="form-label">
                                                                    ¿Cuántos tickets deseas comprar? <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="input-group input-group-lg">
                                                                    <button class="btn btn-outline-secondary" type="button" id="btn_menos">
                                                                        <i class="ri-subtract-line"></i>
                                                                    </button>
                                                                    <input type="number" class="form-control text-center fs-20 fw-semibold" 
                                                                           id="cantidad_tickets" name="cantidad_tickets" value="1" min="1" max="999">
                                                                    <button class="btn btn-outline-secondary" type="button" id="btn_mas">
                                                                        <i class="ri-add-line"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="text-danger small mt-1" id="cantidad_tickets_error" style="display: none;"></div>
                                                                <small class="text-muted d-block mt-2">
                                                                    <i class="ri-information-line"></i> Mientras más tickets compres, mayores probabilidades de ganar
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Tickets Disponibles Info -->
                                                    <div class="alert alert-info border-0 mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ri-ticket-line fs-20 me-2"></i>
                                                            <div>
                                                                <strong>Tickets Disponibles:</strong> 
                                                                <span id="tickets_disponibles_tab">0</span> de <span id="tickets_total_tab">0</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- SISTEMA DE SELECCIÓN DE NÚMEROS DE BOLETO -->
                                                    <div class="card border border-success mb-4" id="seccion_seleccion_numero">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">
                                                                <i class="ri-hashtag text-success me-1"></i> Selecciona tu Número de la Suerte
                                                            </h6>
                                                            
                                                            <!-- Opciones de Selección -->
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <div class="card bg-light border h-100">
                                                                        <div class="card-body text-center p-3">
                                                                            <div class="avatar-sm mx-auto mb-2">
                                                                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                                    <i class="ri-hashtag fs-18"></i>
                                                                                </div>
                                                                            </div>
                                                                            <h6 class="mb-2">¿Tienes un número favorito?</h6>
                                                                            <p class="text-muted small mb-3">Elige tu número de la suerte</p>
                                                                            <button type="button" class="btn btn-primary btn-sm" onclick="mostrarGridNumeros()">
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
                                                                            <h6 class="mb-2">¿Prefieres la sorpresa?</h6>
                                                                            <p class="text-muted small mb-3">Nosotros elegimos por ti</p>
                                                                            <button type="button" class="btn btn-success btn-sm" onclick="asignarNumerosAleatorios()">
                                                                                <i class="ri-refresh-line me-1"></i> Asignar Números Aleatorios
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Números Seleccionados/Reservados -->
                                                            <div id="numero_seleccionado_display" style="display: none;">
                                                                <div class="alert alert-warning border-0 mb-0">
                                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="ri-ticket-2-line fs-20 me-2"></i>
                                                                            <div>
                                                                                <strong>Tus números seleccionados:</strong>
                                                                                <span class="badge bg-info ms-2" id="contador_numeros">0/0</span>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button" class="btn btn-sm btn-light" onclick="cancelarTodasLasSelecciones()">
                                                                            <i class="ri-close-line"></i> Limpiar Todo
                                                                        </button>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap gap-2 mb-2" id="lista_numeros_seleccionados">
                                                                        <!-- Se llenará dinámicamente -->
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <small>
                                                                            <i class="ri-time-line me-1"></i> 
                                                                            Reservado por: <strong id="timer_reserva">10:00</strong>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <input type="hidden" id="numeros_reservados" name="numeros_reservados">
                                                            <input type="hidden" id="numeros_formateados" name="numeros_formateados">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="button" class="btn btn-light btn-label previestab" 
                                                                data-previous="pills-personal-tab">
                                                            <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                            Volver
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                                data-nexttab="pills-payment-tab" id="btn_continuar_orden">
                                                            <i class="ri-bank-card-line label-icon align-middle fs-16 ms-2"></i>
                                                            Continuar a Pago
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tab 3: Información de Pago -->
                                                <div class="tab-pane fade" id="pills-payment" role="tabpanel">
                                                    <div>
                                                        <h5 class="mb-1">Información de Pago</h5>
                                                        <p class="text-muted mb-4">Selecciona tu método de pago preferido</p>
                                                    </div>
                                                    
                                                    <h6 class="fs-14 mb-3">Métodos de Pago Disponibles</h6>
                                                    
                                                    <div class="row g-4">
                                                        <!-- Yape -->
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body text-center p-3">
                                                                    <div class="avatar-sm mx-auto mb-3">
                                                                        <div class="avatar-title bg-success-subtle text-success rounded-circle fs-20">
                                                                            <i class="ri-smartphone-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="mb-3">Yape</h6>
                                                                    <img src="assets/images/landing/qr_dale.png" alt="QR Yape" 
                                                                         class="img-fluid rounded border mb-2" 
                                                                         style="max-width: 140px; height: auto;"
                                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                    <div class="alert alert-secondary border-0 p-2" style="display: none;">
                                                                        <small><i class="ri-qr-code-line"></i> QR no disponible</small>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Número: <strong class="text-dark">51 987 386 463</strong>
                                                                    </p>
                                                                    <small class="text-success">
                                                                        <i class="ri-qr-scan-line"></i> Escanea el QR o usa el número
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Plin -->
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body text-center p-3">
                                                                    <div class="avatar-sm mx-auto mb-3">
                                                                        <div class="avatar-title bg-info-subtle text-info rounded-circle fs-20">
                                                                            <i class="ri-smartphone-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="mb-3">Plin</h6>
                                                                    <img src="assets/images/landing/qr_plim.png" alt="QR Plin" 
                                                                         class="img-fluid rounded border mb-2" 
                                                                         style="max-width: 140px; height: auto;"
                                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                    <div class="alert alert-secondary border-0 p-2" style="display: none;">
                                                                        <small><i class="ri-qr-code-line"></i> QR no disponible</small>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Número: <strong class="text-dark">51 905 573 536</strong>
                                                                    </p>
                                                                    <small class="text-info">
                                                                        <i class="ri-qr-scan-line"></i> Escanea el QR o usa el número
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Interbank -->
                                                        <div hidden class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <div class="avatar-sm me-3">
                                                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                                <i class="ri-bank-line fs-18"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h6 class="mb-0">Banco Interbank</h6>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Cuenta: <strong class="text-dark">[Ingresar cuenta]</strong>
                                                                    </p>
                                                                    <p class="mb-0 small text-muted">
                                                                        CCI: <strong class="text-dark">[Ingresar CCI]</strong>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- BCP -->
                                                        <div hidden class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <div class="avatar-sm me-3">
                                                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                                <i class="ri-bank-line fs-18"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h6 class="mb-0">Banco BCP</h6>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Cuenta: <strong class="text-dark">[Ingresar cuenta]</strong>
                                                                    </p>
                                                                    <p class="mb-0 small text-muted">
                                                                        CCI: <strong class="text-dark">[Ingresar CCI]</strong>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Subir Comprobante -->
                                                    <div class="mt-4">
                                                        <label for="comprobante_pago" class="form-label">
                                                            <i class="ri-image-add-line me-1"></i> Comprobante de Pago 
                                                            <span class="text-muted">(Opcional)</span>
                                                        </label>
                                                        <input type="file" class="form-control" id="comprobante_pago" 
                                                               name="comprobante_pago" accept="image/*,.pdf">
                                                        <div class="text-danger small mt-1" id="comprobante_pago_error" style="display: none;"></div>
                                                        <small class="text-muted d-block mt-2">
                                                            Si ya realizaste el pago, puedes subir tu comprobante ahora. Formatos: JPG, PNG, PDF (máx. 5MB)
                                                        </small>
                                                        
                                                        <!-- Vista previa -->
                                                        <div id="preview_comprobante" class="mt-3" style="display: none;">
                                                            <div class="alert alert-success border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="ri-file-check-line fs-20 me-2"></i>
                                                                    <div>
                                                                        <strong>Archivo seleccionado:</strong>
                                                                        <p class="mb-0 small" id="nombre_archivo"></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="alert alert-warning border-0 mt-3">
                                                        <small>
                                                            <i class="ri-information-line me-1"></i>
                                                            <strong>Importante:</strong> Una vez realizado el pago, sube tu comprobante o envíalo por WhatsApp para validar tu compra.
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="button" class="btn btn-light btn-label previestab" 
                                                                data-previous="pills-order-tab">
                                                            <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                            Volver
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                                data-nexttab="pills-finish-tab" id="btn_continuar_pago">
                                                            <i class="ri-checkbox-circle-line label-icon align-middle fs-16 ms-2"></i>
                                                            Continuar a Confirmar
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tab 4: Confirmar -->
                                                <div class="tab-pane fade" id="pills-finish" role="tabpanel">
                                                    <div class="text-center py-4">
                                                        <div class="mb-4">
                                                            <i class="ri-checkbox-circle-line text-success" style="font-size: 72px;"></i>
                                                        </div>
                                                        <h5 class="mb-2">Revisa tu Orden</h5>
                                                        <p class="text-muted">Verifica que toda la información sea correcta antes de confirmar</p>
                                                    </div>
                                                    
                                                    <!-- Resumen Final -->
                                                    <div class="card bg-light border-0 mb-4">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">Resumen de tu Compra</h6>
                                                            <div class="table-responsive">
                                                                <table class="table table-borderless mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-muted">Rifa:</td>
                                                                            <td class="text-end fw-semibold" id="resumen_rifa_nombre">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Participante:</td>
                                                                            <td class="text-end fw-semibold" id="resumen_nombre">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Email:</td>
                                                                            <td class="text-end" id="resumen_email">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Teléfono:</td>
                                                                            <td class="text-end" id="resumen_telefono">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Documento:</td>
                                                                            <td class="text-end" id="resumen_documento">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Dirección de Envío:</td>
                                                                            <td class="text-end" id="resumen_direccion">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Ciudad/Estado:</td>
                                                                            <td class="text-end" id="resumen_ubicacion">-</td>
                                                                        </tr>
                                                                        <tr id="resumen_numero_row" style="display: none;">
                                                                            <td class="text-muted">
                                                                                <i class="ri-hashtag me-1"></i>Números de Boleto:
                                                                            </td>
                                                                            <td class="text-end">
                                                                                <div class="d-flex flex-wrap gap-1 justify-content-end" id="resumen_numeros_boletos">
                                                                                    <!-- Se llenará dinámicamente -->
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Cantidad de Tickets:</td>
                                                                            <td class="text-end fw-semibold" id="resumen_cantidad">1</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Precio por Ticket:</td>
                                                                            <td class="text-end" id="resumen_precio">0.00</td>
                                                                        </tr>
                                                                        <tr class="border-top">
                                                                            <td class="fw-semibold fs-15">Total a Pagar:</td>
                                                                            <td class="text-end fw-bold text-success fs-18" id="resumen_total">0.00</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Términos y Condiciones -->
                                                    <div class="form-check mb-4">
                                                        <input class="form-check-input" type="checkbox" id="acepto_terminos" name="acepto_terminos">
                                                        <label class="form-check-label" for="acepto_terminos">
                                                            Acepto los <a href="terminos" target="_blank" class="text-primary">términos y condiciones</a> 
                                                            y las <a href="terminos" target="_blank" class="text-primary">bases del sorteo</a> 
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="text-danger small mt-1" id="acepto_terminos_error" style="display: none;"></div>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="button" class="btn btn-light btn-label previestab" 
                                                                data-previous="pills-payment-tab">
                                                            <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                            Volver
                                                        </button>
                                                        <button type="submit" class="btn btn-success btn-label right ms-auto" id="btn_realizar_compra" disabled>
                                                            <i class="ri-shopping-bag-line label-icon align-middle fs-16 ms-2"></i>
                                                            Confirmar Compra
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <!-- end tab content -->
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Columna Derecha - Resumen de la Orden -->
                                <div class="col-xl-4">
                                    <div class="card">
                                        <div class="card-header bg-success-subtle border-0">
                                            <h5 class="card-title mb-0">
                                                <i class="ri-shopping-bag-line me-2"></i>Resumen de Orden
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            
                                            <!-- Premios -->
                                            <div class="mb-3">
                                                <h6 class="fs-14 mb-3">
                                                    <i class="ri-trophy-line text-warning me-1"></i> Premios de esta Rifa
                                                </h6>
                                                <div id="lista_premios" class="d-flex flex-column gap-2">
                                                    <!-- Se llenará dinámicamente -->
                                                </div>
                                            </div>
                                            
                                            <hr class="my-3">
                                            
                                            <!-- Detalles de Precio -->
                                            <div class="table-responsive table-card">
                                                <table class="table table-borderless align-middle mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted">Precio por Ticket:</td>
                                                            <td class="text-end fw-semibold"><span id="precio_ticket">0.00</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Cantidad:</td>
                                                            <td class="text-end fw-semibold"><span id="cantidad_display">1</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Tickets Disponibles:</td>
                                                            <td class="text-end">
                                                                <span class="badge bg-primary-subtle text-primary">
                                                                    <span id="tickets_disponibles">0</span>/<span id="tickets_total">0</span>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr class="table-active">
                                                            <th class="fs-15">Total:</th>
                                                            <th class="text-end">
                                                                <span class="fw-semibold text-success fs-18">
                                                                    <span id="total_pagar">0.00</span>
                                                                </span>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="alert alert-success border-0 mt-3 mb-0">
                                                <small class="mb-0">
                                                    <i class="ri-information-line me-1"></i>
                                                    Recibirás un correo de confirmación al completar tu compra
                                                </small>
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
        <!-- end modal -->

        <!-- Modal para Seleccionar Número de Boleto -->
        <div class="modal fade" id="modal_seleccionar_numero" tabindex="-1" aria-labelledby="modal_seleccionar_numero_label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary-subtle border-0">
                        <h5 class="modal-title" id="modal_seleccionar_numero_label">
                            <i class="ri-hashtag me-2"></i>Selecciona tu Número de la Suerte
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        
                        <!-- Buscador de Número -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                                    <input type="text" class="form-control" id="buscar_numero" 
                                           placeholder="Busca tu número favorito (ej: 777, 888, 1234...)">
                                    <button class="btn btn-primary" type="button" onclick="buscarNumero()">
                                        Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('todos')">
                                        Todos
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('pares')">
                                        Pares
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('impares')">
                                        Impares
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('multiplos5')">
                                        Múltiplos de 5
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Estadísticas de Números -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">Disponibles</div>
                                        <h5 class="mb-0 text-success" id="stat_disponibles">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">Vendidos</div>
                                        <h5 class="mb-0 text-danger" id="stat_vendidos">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">Reservados</div>
                                        <h5 class="mb-0 text-warning" id="stat_reservados">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">% Vendido</div>
                                        <h5 class="mb-0 text-primary" id="stat_porcentaje">0%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Grid de Números -->
                        <div class="alert alert-info border-0 mb-3">
                            <small>
                                <i class="ri-information-line me-1"></i>
                                <strong>Leyenda:</strong> 
                                <span class="badge bg-success ms-2">Disponible</span>
                                <span class="badge bg-secondary ms-1">Vendido</span>
                                <span class="badge bg-warning ms-1">Reservado</span>
                                <span class="badge bg-dark ms-1">Bloqueado</span>
                            </small>
                        </div>
                        
                        <div class="card border">
                            <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
                                <div id="grid_numeros_disponibles" class="row g-2">
                                    <!-- Se llenará dinámicamente con JavaScript -->
                                    <div class="col-12 text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="text-muted mt-2">Cargando números disponibles...</p>
                                    </div>
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
        <!-- end modal seleccionar número -->

        <!-- Modal para Ver Premios -->
        <div class="modal fade" id="modal_ver_premios" tabindex="-1" aria-labelledby="modal_ver_premios_label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary-subtle">
                        <h5 class="modal-title" id="modal_ver_premios_label">
                            <i class="ri-gift-line me-2"></i><span id="modal_premios_rifa_nombre">Premios de la Rifa</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="galeria_premios" class="row g-4">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal ver premios -->

        <!-- Modal visor de imagen (zoom, pantalla grande, prev/next) -->
        <div class="modal fade" id="modal_visor_imagen_premio" tabindex="-1" aria-labelledby="modal_visor_imagen_label" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content bg-dark">
                    <div class="modal-header border-secondary py-2">
                        <h5 class="modal-title text-white" id="modal_visor_imagen_label">
                            <i class="ri-image-line me-2"></i> Vista previa
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-white-50 small" id="visor_imagen_contador">1 / 1</span>
                            <button type="button" class="btn btn-sm btn-outline-light" id="visor_zoom_out" title="Reducir">
                                <i class="ri-subtract-line"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light" id="visor_zoom_in" title="Aumentar">
                                <i class="ri-add-line"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light" id="visor_zoom_reset" title="Tamaño normal">100%</button>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                    </div>
                    <div class="modal-body d-flex align-items-center justify-content-center overflow-auto p-2" id="visor_imagen_contenedor">
                        <img id="visor_imagen_img" src="" alt="Vista previa" class="img-fluid" style="max-width: none; transition: transform 0.2s ease;">
                    </div>
                    <div class="modal-footer border-secondary py-2 justify-content-between">
                        <button type="button" class="btn btn-outline-light btn-sm" id="visor_prev" title="Anterior">
                            <i class="ri-arrow-left-s-line me-1"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm" id="visor_next" title="Siguiente">
                            Siguiente <i class="ri-arrow-right-s-line ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal visor imagen -->

        <!-- Modal resultado consulta Mis Tickets -->
        <div class="modal fade" id="modal_mis_tickets_resultado" tabindex="-1" aria-labelledby="modal_mis_tickets_resultado_label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success-subtle">
                        <h5 class="modal-title" id="modal_mis_tickets_resultado_label">
                            <i class="ri-ticket-line me-2"></i> Tus Tickets
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modal_mis_tickets_body">
                        <!-- Se llena con JS -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" id="btn_copiar_tickets">
                            <i class="ri-file-copy-line me-1"></i> Copiar
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btn_compartir_tickets">
                            <i class="ri-share-line me-1"></i> Compartir
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal mis tickets resultado -->

        <!-- Modal mensaje de contacto enviado -->
        <div class="modal fade" id="modal_contacto_enviado" tabindex="-1" aria-labelledby="modal_contacto_enviado_label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success-subtle border-success">
                        <h5 class="modal-title text-success" id="modal_contacto_enviado_label">
                            <i class="ri-checkbox-circle-line me-2"></i> Mensaje enviado
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <p class="mb-0">Gracias por contactarnos. Hemos recibido tu mensaje y te responderemos a la brevedad posible.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal contacto enviado -->

