<!-- Modal para Crear/Editar Categoría -->
<div class="modal fade" id="modal_categoria" tabindex="-1" aria-labelledby="modal_categoria_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_categoria_label">
                    <i class="ri-price-tag-3-line me-2"></i><span id="modal_categoria_title">Nueva Categoría</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_categoria" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="categoria_id" name="categoria_id">
                    <input type="hidden" id="sede_id_categoria" name="sede_id_categoria">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre_categoria" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria"
                                placeholder="Ejemplo: Electrónica" required>
                            <div class="invalid-feedback" id="nombre_categoria_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="orden_categoria" class="form-label">Orden</label>
                            <input type="number" class="form-control" id="orden_categoria" name="orden_categoria"
                                min="0" value="0">
                            <div class="invalid-feedback" id="orden_categoria_error"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="descripcion_categoria" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_categoria" name="descripcion_categoria"
                                rows="3" placeholder="Describe brevemente la categoría"></textarea>
                            <div class="invalid-feedback" id="descripcion_categoria_error"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="icono_categoria" class="form-label">Icono</label>
                            <input type="text" class="form-control" id="icono_categoria" name="icono_categoria"
                                placeholder="Clase de icono o URL">
                            <div class="invalid-feedback" id="icono_categoria_error"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="color_categoria" class="form-label">Color</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="color_categoria" name="color_categoria"
                                    placeholder="#FF0000">
                                <span class="input-group-text p-0">
                                    <input type="color" class="form-control form-control-color border-0" id="color_categoria_picker" value="#ffffff" title="Elegir color">
                                </span>
                            </div>
                            <div class="invalid-feedback" id="color_categoria_error"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="estado_categoria" class="form-label">Estado</label>
                            <select class="form-select" id="estado_categoria" name="estado_categoria">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            <div class="invalid-feedback" id="estado_categoria_error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_categoria">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


