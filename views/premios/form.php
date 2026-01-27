<!-- Modal para Crear/Editar Premio -->
<div class="modal fade" id="modal_premio" tabindex="-1" aria-labelledby="modal_premio_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_premio_label">
                    <i class="ri-gift-line me-2"></i><span id="modal_premio_title">Nuevo Premio</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_premio" novalidate enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="premio_id" name="premio_id">
                    <input type="hidden" id="sede_id" name="sede_id">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre del Premio <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                placeholder="Premio principal">
                            <div class="invalid-feedback" id="nombre_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id">
                                <option value="">Sin categoría</option>
                            </select>
                            <div class="invalid-feedback" id="categoria_id_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="valor_estimado" class="form-label">
                                Valor estimado (<span id="valor_estimado_simbolo">S/.</span>)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" id="valor_estimado_simbolo_prefix">S/.</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="valor_estimado"
                                    name="valor_estimado" placeholder="0.00">
                            </div>
                            <div class="invalid-feedback" id="valor_estimado_error"></div>
                        </div>

                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                placeholder="Describe el premio, características, etc."></textarea>
                            <div class="invalid-feedback" id="descripcion_error"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="marca" name="marca"
                                placeholder="Marca del premio">
                        </div>
                        <div class="col-md-4">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo"
                                placeholder="Modelo del premio">
                        </div>
                        <div class="col-md-4">
                            <label for="color" class="form-label">Color</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="color" name="color"
                                    placeholder="Color">
                                <span class="input-group-text p-0">
                                    <input type="color" class="form-control form-control-color border-0" id="color_picker" value="#ffffff" title="Elegir color">
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="imagen_principal" class="form-label">Imagen principal</label>
                            <input type="file" class="form-control" id="imagen_principal" name="imagen_principal" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo recomendado 2MB.</div>
                            <input type="hidden" id="imagen_principal_actual" name="imagen_principal_actual" value="">
                            <div class="form-text text-muted" id="imagen_principal_actual_text">Sin imagen cargada</div>
                            <div class="mt-2" id="imagen_principal_preview">
                                <span class="text-muted small">Sin imagen seleccionada</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="imagen_secundaria" class="form-label">Imagen secundaria</label>
                            <input type="file" class="form-control" id="imagen_secundaria" name="imagen_secundaria" accept="image/*">
                            <input type="hidden" id="imagen_secundaria_actual" name="imagen_secundaria_actual" value="">
                            <div class="form-text text-muted" id="imagen_secundaria_actual_text">Sin imagen cargada</div>
                            <div class="mt-2" id="imagen_secundaria_preview">
                                <span class="text-muted small">Sin imagen seleccionada</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="galeria_imagenes" class="form-label">Galería de imágenes</label>
                            <input type="file" class="form-control" id="galeria_imagenes" name="galeria_imagenes[]" accept="image/*" multiple>
                            <input type="hidden" id="galeria_imagenes_actual" name="galeria_imagenes_actual" value="[]">
                            <div class="form-text text-muted">Selecciona una o varias imágenes adicionales para el premio.</div>
                            <ul class="small text-muted mt-2 mb-0" id="galeria_actual_list">
                                <li class="text-muted">Sin imágenes cargadas</li>
                            </ul>
                            <div class="mt-3" id="galeria_nuevas_preview">
                                <span class="text-muted small">Sin archivos seleccionados</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="video_url" class="form-label">Video (URL)</label>
                            <input type="url" class="form-control" id="video_url" name="video_url"
                                placeholder="https://youtube.com/...">
                        </div>
                        <div class="col-md-4">
                            <label for="especificaciones" class="form-label">Especificaciones</label>
                            <textarea class="form-control" id="especificaciones" name="especificaciones" rows="2"
                                placeholder="Detalles técnicos o características"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="terminos_condiciones" class="form-label">Condiciones de entrega</label>
                            <textarea class="form-control" id="terminos_condiciones" name="terminos_condiciones" rows="2"
                                placeholder="Ejemplo: el premio se entrega dentro de 7 días hábiles."></textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="restricciones" class="form-label">Restricciones</label>
                            <textarea class="form-control" id="restricciones" name="restricciones" rows="2"
                                placeholder="Restricciones específicas"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="es_destacado" class="form-label">Destacado</label>
                            <select class="form-select" id="es_destacado" name="es_destacado">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="orden_visualizacion" class="form-label">Orden visualización</label>
                            <input type="number" class="form-control" id="orden_visualizacion"
                                name="orden_visualizacion" min="0" value="0">
                        </div>
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                                <option value="2">Agotado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_premio">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

