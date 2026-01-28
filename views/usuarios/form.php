<!-- Modal para Crear/Editar Usuario -->
<div class="modal fade" id="modal_usuario" tabindex="-1" aria-labelledby="modal_usuario_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_usuario_label">
                    <i class="ri-user-settings-line me-2"></i><span id="modal_usuario_title">Nuevo Usuario</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_usuario" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="usuario_id" name="usuario_id">
                    <input type="hidden" id="usuario_sede_id" name="usuario_sede_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="usuario_username" class="form-label">Usuario (login) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="usuario_username" name="usuario_username"
                                placeholder="nombre.usuario" required>
                            <div class="invalid-feedback" id="usuario_username_error"></div>
                        </div>
                        <div class="col-md-6" id="cont_usuario_password">
                            <label for="usuario_password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="usuario_password" name="usuario_password"
                                placeholder="Mín. 6 caracteres" autocomplete="new-password">
                            <div class="invalid-feedback" id="usuario_password_error"></div>
                            <small class="text-muted">Solo obligatoria al registrar. En edición déjelo en blanco para no cambiar.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="usuario_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="usuario_email" name="usuario_email"
                                placeholder="correo@ejemplo.com" required>
                            <div class="invalid-feedback" id="usuario_email_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="usuario_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="usuario_telefono" name="usuario_telefono"
                                placeholder="+51 999 999 999">
                        </div>

                        <div class="col-md-4">
                            <label for="usuario_primer_nombre" class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="usuario_primer_nombre" name="usuario_primer_nombre"
                                placeholder="Nombres" required>
                            <div class="invalid-feedback" id="usuario_primer_nombre_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="usuario_apellido_paterno" class="form-label">Ap. Paterno <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="usuario_apellido_paterno" name="usuario_apellido_paterno"
                                placeholder="Apellido paterno" required>
                            <div class="invalid-feedback" id="usuario_apellido_paterno_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="usuario_apellido_materno" class="form-label">Ap. Materno</label>
                            <input type="text" class="form-control" id="usuario_apellido_materno" name="usuario_apellido_materno"
                                placeholder="Apellido materno">
                        </div>

                        <div class="col-md-6">
                            <label for="usuario_rol_id" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="usuario_rol_id" name="usuario_rol_id" required>
                                <option value="">Seleccione rol...</option>
                            </select>
                            <div class="invalid-feedback" id="usuario_rol_id_error"></div>
                        </div>

                        <div class="col-md-6" id="cont_usuario_estado">
                            <label for="usuario_estado" class="form-label">Estado</label>
                            <select class="form-select" id="usuario_estado" name="usuario_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_usuario">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
