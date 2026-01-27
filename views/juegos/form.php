<!-- Modal para Seleccionar Premio y Jugar -->
<div class="modal fade" id="modal_jugar_rifa" tabindex="-1" aria-labelledby="modal_jugar_rifa_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title" id="modal_jugar_rifa_label">
                    <i class="ri-gamepad-line me-2"></i>Jugar Rifa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="juego_rifa_id" name="juego_rifa_id">
                <input type="hidden" id="juego_sede_id" name="juego_sede_id">

                <!-- Paso 1: Seleccionar Premio -->
                <div id="juego_paso_seleccionar_premio">
                    <div class="card border-primary mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="ri-information-line me-2"></i>Información de la Rifa
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Código:</strong> <span id="juego_codigo_rifa">-</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Nombre:</strong> <span id="juego_nombre_rifa">-</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Participantes:</strong> 
                                    <span class="badge bg-secondary" id="juego_total_participantes">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Números Vendidos:</strong> 
                                    <span class="badge bg-warning" id="juego_numeros_vendidos">0</span> / 
                                    <span id="juego_total_numeros">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Intento Ganador:</strong> 
                                    <span class="badge bg-success" id="juego_intento_ganador">5</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="ri-gift-line me-2"></i>Seleccionar Premio a Jugar
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="juego_lista_premios">
                                <p class="text-muted text-center">Cargando premios...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Jugar Premio -->
                <div id="juego_paso_jugar" style="display: none;">
                    <div class="card border-primary mb-3">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="ri-gift-line me-2"></i>Premio Seleccionado
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Premio:</strong> <span id="juego_premio_nombre">-</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Código:</strong> <span id="juego_premio_codigo">-</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Intento Actual:</strong> 
                                    <span class="badge bg-info" id="juego_intento_actual">0</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Intento Ganador:</strong> 
                                    <span class="badge bg-success" id="juego_intento_ganador_premio">5</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total Intentos:</strong> 
                                    <span class="badge bg-primary" id="juego_total_intentos">5</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resultado del Juego -->
                    <div id="juego_resultado_container" style="display: none;">
                        <div class="alert alert-info border-0 mb-3" id="juego_resultado_info">
                            <div class="d-flex align-items-center">
                                <i class="ri-information-line fs-20 me-2"></i>
                                <div>
                                    <strong id="juego_resultado_titulo">Participante Seleccionado</strong>
                                    <p class="mb-0 mt-1" id="juego_resultado_mensaje"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Participantes -->
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="ri-user-line me-2"></i>Participantes
                            </h6>
                        </div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            <div id="juego_lista_participantes">
                                <p class="text-muted text-center">Cargando participantes...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Registrar Ganador -->
                <div id="juego_paso_registrar_ganador" style="display: none;">
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success-subtle">
                            <h6 class="card-title mb-0 text-success">
                                <i class="ri-trophy-fill me-2"></i>¡Ganador Encontrado!
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="form_registrar_ganador" novalidate>
                                <input type="hidden" id="ganador_rifa_id">
                                <input type="hidden" id="ganador_rifa_premio_id">
                                <input type="hidden" id="ganador_premio_id">
                                <input type="hidden" id="ganador_persona_id">
                                <input type="hidden" id="ganador_ticket_id">
                                <input type="hidden" id="ganador_numero_id">
                                <input type="hidden" id="ganador_intento_ganador">

                                <div class="alert alert-success border-0 mb-3">
                                    <strong id="ganador_nombre_completo">-</strong><br>
                                    <small id="ganador_documento">-</small><br>
                                    <small class="text-muted" id="ganador_numeros" style="display: none;">
                                        <strong>Número ganador:</strong> <span id="ganador_numeros_lista">-</span>
                                    </small>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="ganador_direccion_envio" class="form-label">Dirección de Envío (Opcional)</label>
                                        <input type="text" class="form-control" id="ganador_direccion_envio" name="ganador_direccion_envio"
                                            placeholder="Ingrese la dirección para el envío del premio">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ganador_ciudad_envio" class="form-label">Ciudad</label>
                                        <input type="text" class="form-control" id="ganador_ciudad_envio" name="ganador_ciudad_envio"
                                            placeholder="Ciudad">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ganador_pais_envio" class="form-label">País</label>
                                        <input type="text" class="form-control" id="ganador_pais_envio" name="ganador_pais_envio"
                                            placeholder="País" value="Perú">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="ganador_publicar_web" name="ganador_publicar_web" value="1">
                                            <label class="form-check-label" for="ganador_publicar_web">
                                                Publicar ganador en la página web
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="btn_cerrar_modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
                <button type="button" class="btn btn-secondary" id="btn_volver_seleccionar" style="display: none;">
                    <i class="ri-arrow-left-line me-1"></i>Volver
                </button>
                <button type="button" class="btn btn-primary" id="btn_jugar_rifa" style="display: none;">
                    <i class="ri-gamepad-line me-1"></i>Jugar
                </button>
                <button type="button" class="btn btn-success" id="btn_registrar_ganador" style="display: none;">
                    <i class="ri-save-line me-1"></i>Registrar Ganador
                </button>
            </div>
        </div>
    </div>
</div>
