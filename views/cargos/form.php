<!-- Modal para Crear/Editar Cargo -->
<div class="modal fade" id="modal_cargo" tabindex="-1" aria-labelledby="modal_cargo_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_cargo_label">
                    <i class="ri-briefcase-line me-2"></i><span id="modal_title">Nuevo Cargo</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_cargo" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="cargo_id" name="cargo_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_cargo" class="form-label">
                                    Nombre del Cargo <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nombre_cargo" name="nombre_cargo"
                                    placeholder="Ingrese el nombre del cargo" required
                                    style="min-height: 45px; font-size: 1rem;">
                                <div class="invalid-feedback" id="nombre_cargo_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="salario_base" class="form-label">
                                    Salario Base
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">S/.</span>
                                    <input type="number" class="form-control" id="salario_base" name="salario_base"
                                        placeholder="0.00" min="0" step="0.01"
                                        style="min-height: 45px; font-size: 1rem;">
                                </div>
                                <div class="invalid-feedback" id="salario_base_error"></div>
                            </div>
                        </div>
                    </div>
                    <!-- 
                    <div class="row">
                       <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">
                                    Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="estado" name="estado" required 
                                        style="min-height: 45px; font-size: 1rem;">
                                    <option value="">Seleccione un estado</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                <div class="invalid-feedback" id="estado_error"></div>
                            </div>
                        </div>  
                    </div> -->

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                            placeholder="Ingrese una descripción del cargo (opcional)"
                            style="min-height: 80px; font-size: 1rem;"></textarea>
                        <div class="invalid-feedback" id="descripcion_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        style="min-height: 45px; font-size: 1rem; padding: 0.75rem 1.5rem;">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar"
                        style="min-height: 45px; font-size: 1rem; padding: 0.75rem 1.5rem;">
                        <i class="ri-save-line me-1"></i><span id="btn_guardar_text">Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>