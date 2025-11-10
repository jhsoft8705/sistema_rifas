<!-- Modal para Vincular Empleado con Dispositivo Biométrico -->
<div class="modal fade" id="modal_biometrico" tabindex="-1" aria-labelledby="modal_biometrico_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_biometrico_label">
                    <i class="ri-fingerprint-line me-2"></i><span id="modal_title_biometrico">Vincular Biométrico</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_biometrico" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="biometrico_empleado_id" name="biometrico_empleado_id">
                    
                    <div class="alert alert-info" role="alert">
                        <i class="ri-information-line me-2"></i>
                        <strong>Empleado:</strong> <span id="biometrico_empleado_nombre"></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="biometric_user_id" class="form-label">
                                    ID Usuario Biométrico <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="biometric_user_id" name="biometric_user_id"
                                    placeholder="Ej: 10" required>
                                <small class="text-muted d-block mt-1">
                                    <i class="ri-information-line"></i> Código del empleado en el sistema biométrico (emp_code)
                                </small>
                                <div class="invalid-feedback" id="biometric_user_id_error"></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="device_id" class="form-label">
                                    ID Dispositivo <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="device_id" name="device_id" required>
                                    <option value="">Seleccione un dispositivo</option>
                                    <!-- Las opciones se cargarán dinámicamente -->
                                </select>
                                <small class="text-muted d-block mt-1">
                                    <i class="ri-information-line"></i> Seleccione el dispositivo biométrico
                                </small>
                                <div class="invalid-feedback" id="device_id_error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Cancelar y cerrar el modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_biometrico"
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Guardar vinculación biométrica">
                        <i class="ri-save-line me-1"></i>Guardar Vinculación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

