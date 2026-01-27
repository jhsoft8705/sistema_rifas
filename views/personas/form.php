<!-- Modal para Crear/Editar Persona -->
<div class="modal fade" id="modal_persona" tabindex="-1" aria-labelledby="modal_persona_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_persona_label">
                    <i class="ri-user-line me-2"></i><span id="modal_persona_title">Nueva Persona</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_persona" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="persona_id" name="persona_id">
                    <input type="hidden" id="sede_id" name="sede_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required
                                placeholder="Ingrese los nombres">
                            <div class="invalid-feedback" id="nombres_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required
                                placeholder="Ingrese los apellidos">
                            <div class="invalid-feedback" id="apellidos_error"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="tipo_documento" class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                <option value="">Seleccione...</option>
                                <option value="DNI">DNI</option>
                                <option value="CE">Carné de Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                            <div class="invalid-feedback" id="tipo_documento_error"></div>
                        </div>
                        <div class="col-md-8">
                            <label for="numero_documento" class="form-label">Número de Documento <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="numero_documento" name="numero_documento" required
                                placeholder="Ingrese el número de documento">
                            <div class="invalid-feedback" id="numero_documento_error"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="correo@ejemplo.com">
                            <div class="invalid-feedback" id="email_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono / WhatsApp</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono"
                                placeholder="+51 999 999 999">
                            <div class="invalid-feedback" id="telefono_error"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion"
                                placeholder="Ingrese la dirección">
                            <div class="invalid-feedback" id="direccion_error"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad"
                                placeholder="Ingrese la ciudad">
                            <div class="invalid-feedback" id="ciudad_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="pais" class="form-label">País</label>
                            <input type="text" class="form-control" id="pais" name="pais"
                                placeholder="Ingrese el país" value="Perú">
                            <div class="invalid-feedback" id="pais_error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_persona">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
