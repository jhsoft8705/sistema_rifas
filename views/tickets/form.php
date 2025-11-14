<!-- Modal para Validar Ticket -->
<div class="modal fade" id="modal_validar_ticket" tabindex="-1" aria-labelledby="modal_validar_ticket_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_validar_ticket_label">
                    <i class="ri-ticket-line me-2"></i>Validar Ticket
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_validar_ticket" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="ticket_id_validar" name="ticket_id">
                    <input type="hidden" id="sede_id_ticket_validar" name="sede_id">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong><i class="ri-ticket-line me-1"></i>Información del Ticket:</strong>
                                <div id="info_ticket_validar" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="alert alert-warning" id="alert_numero_ticket" style="display: none;">
                                <strong><i class="ri-alert-line me-1"></i>Número Reservado:</strong>
                                <div id="info_numero_ticket" class="mt-1"></div>
                                <small class="text-muted d-block mt-2">
                                    <i class="ri-information-line me-1"></i>
                                    Si rechazas este ticket, el número será liberado y estará disponible para otros participantes.
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Comprobante de Pago</label>
                            <div id="preview_comprobante_ticket" class="text-center border rounded p-3 mb-3" style="min-height: 200px;">
                                <p class="text-muted">Cargando información...</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Precio Pagado</label>
                            <input type="text" class="form-control" id="precio_ticket_validar" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Compra</label>
                            <input type="text" class="form-control" id="fecha_compra_ticket_validar" readonly>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Acción <span class="text-danger">*</span></label>
                            <select class="form-select" id="accion_ticket" name="accion_ticket" required>
                                <option value="">Seleccione una acción</option>
                                <option value="APROBADO">Aprobar ticket</option>
                                <option value="RECHAZADO">Rechazar ticket</option>
                            </select>
                            <div class="invalid-feedback">Seleccione una acción</div>
                        </div>
                        
                        <div class="col-12 d-none" id="contenedor_motivo_rechazo_ticket">
                            <label class="form-label">Motivo de rechazo <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="motivo_rechazo_ticket" name="motivo_rechazo_ticket" rows="3" 
                                placeholder="Indique el motivo por el cual se rechaza el ticket"></textarea>
                            <div class="invalid-feedback">El motivo de rechazo es obligatorio</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_validacion_ticket">
                        <i class="ri-save-line me-1"></i>Guardar Validación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

