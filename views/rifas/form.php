<!-- Modal para crear/editar rifa -->
<div class="modal fade" id="modal_rifa" tabindex="-1" aria-labelledby="modal_rifa_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_rifa_label">
                    <i class="ri-ticket-2-line me-2"></i><span id="modal_rifa_title">Nueva Rifa</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_rifa" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="rifa_id" name="rifa_id">
                    <input type="hidden" id="sede_id_rifa" name="sede_id">
                    <div class="alert alert-soft-primary d-flex align-items-start gap-3">
                        <i class="ri-lightbulb-flash-line fs-4"></i>
                        <div>
                            <strong>Sugerencia:</strong> Una vez guardada la rifa podrás administrar premios adicionales y números desde los botones de acciones.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="premio_id" class="form-label">Premio principal</label>
                            <select class="form-select" id="premio_id" name="premio_id">
                                <option value="">Sin premio principal</option>
                            </select>
                            <div class="invalid-feedback" id="premio_id_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="estado_rifa" class="form-label">Estado</label>
                            <select class="form-select" id="estado_rifa" name="estado_rifa">
                                <option value="BORRADOR">Borrador</option>
                                <option value="PUBLICADA">Publicada</option>
                                <option value="EN_VENTA">En venta</option>
                                <option value="CERRADA">Cerrada</option>
                                <option value="SORTEO_REALIZADO">Sorteo realizado</option>
                                <option value="FINALIZADA">Finalizada</option>
                                <option value="CANCELADA">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="nombre_rifa" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_rifa" name="nombre_rifa" placeholder="Nombre del sorteo" required>
                            <div class="invalid-feedback" id="nombre_rifa_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="precio_ticket" class="form-label">Precio ticket (S/.) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="precio_ticket" name="precio_ticket" min="0" step="0.01" required>
                            <div class="invalid-feedback" id="precio_ticket_error"></div>
                        </div>
                        <div class="col-12">
                            <label for="descripcion_rifa" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_rifa" name="descripcion_rifa" rows="2" placeholder="Descripción del sorteo"></textarea>
                        </div>

                        <div class="col-md-3">
                            <label for="numero_inicial" class="form-label">Número inicial <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="numero_inicial" name="numero_inicial" min="0" required>
                            <div class="invalid-feedback" id="numero_inicial_error"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="numero_final" class="form-label">Número final <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="numero_final" name="numero_final" min="1" required>
                            <div class="invalid-feedback" id="numero_final_error"></div>
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad_digitos" class="form-label">Cantidad de dígitos</label>
                            <input type="number" class="form-control" id="cantidad_digitos" name="cantidad_digitos" min="1" value="4">
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad_maxima_por_persona" class="form-label">Máximo por participante</label>
                            <input type="number" class="form-control" id="cantidad_maxima_por_persona" name="cantidad_maxima_por_persona" min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad_maxima_tickets" class="form-label">Tickets máximos</label>
                            <input type="number" class="form-control" id="cantidad_maxima_tickets" name="cantidad_maxima_tickets" min="0" placeholder="Ilimitado">
                        </div>
                        <div class="col-md-3">
                            <label for="tipo_numeracion" class="form-label">Tipo de numeración</label>
                            <select class="form-select" id="tipo_numeracion" name="tipo_numeracion">
                                <option value="CORRELATIVO">Correlativo</option>
                                <option value="ALEATORIO">Aleatorio</option>
                                <option value="PERSONALIZADO">Personalizado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_inicio_venta" class="form-label">Fecha inicio venta <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-date" id="fecha_inicio_venta" name="fecha_inicio_venta" required>
                            <div class="invalid-feedback" id="fecha_inicio_venta_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin_venta" class="form-label">Fecha fin venta <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-date" id="fecha_fin_venta" name="fecha_fin_venta" required>
                            <div class="invalid-feedback" id="fecha_fin_venta_error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_sorteo" class="form-label">Fecha del sorteo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control flatpickr-date" id="fecha_sorteo" name="fecha_sorteo" required>
                            <div class="invalid-feedback" id="fecha_sorteo_error"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="prefijo_numero" class="form-label">Prefijo del número</label>
                            <input type="text" class="form-control" id="prefijo_numero" name="prefijo_numero" placeholder="RIFA-">
                        </div>
                        <div class="col-md-6">
                            <label for="sufijo_numero" class="form-label">Sufijo del número</label>
                            <input type="text" class="form-control" id="sufijo_numero" name="sufijo_numero" placeholder="-2025">
                        </div>
                        <div class="col-md-4">
                            <label for="mostrar_contador" class="form-label">Mostrar contador</label>
                            <select class="form-select" id="mostrar_contador" name="mostrar_contador">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="mostrar_participantes" class="form-label">Mostrar participantes</label>
                            <select class="form-select" id="mostrar_participantes" name="mostrar_participantes">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="mostrar_tickets_vendidos" class="form-label">Mostrar tickets vendidos</label>
                            <select class="form-select" id="mostrar_tickets_vendidos" name="mostrar_tickets_vendidos">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="texto_promocional" class="form-label">Texto promocional</label>
                            <textarea class="form-control" id="texto_promocional" name="texto_promocional" rows="2"
                                placeholder="Mensaje corto para landing o redes sociales"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="reglas_participacion" class="form-label">Reglas de participación</label>
                            <textarea class="form-control" id="reglas_participacion" name="reglas_participacion" rows="2"
                                placeholder="Condiciones y reglas del sorteo"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="terminos_rifa" class="form-label">Términos y condiciones</label>
                            <textarea class="form-control" id="terminos_rifa" name="terminos_rifa" rows="2"
                                placeholder="Términos y condiciones específicos"></textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="permitir_seleccion_numero" name="permitir_seleccion_numero" checked>
                                <label class="form-check-label" for="permitir_seleccion_numero">Permitir selección manual</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="asignacion_automatica" name="asignacion_automatica" checked>
                                <label class="form-check-label" for="asignacion_automatica">Asignación automática</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-soft-warning d-flex align-items-start gap-2 mb-0">
                                <i class="ri-alert-line fs-4"></i>
                                <div>
                                    Al guardar se generarán automáticamente las cartillas del rango indicado. Podrás regenerarlas en edición siempre que no existan tickets asignados.
                                </div>
                            </div>
                        </div>
                        <div class="col-12 d-none" id="contenedor_regenerar_numeros">
                            <div class="alert alert-soft-info d-flex align-items-start gap-2 mb-2" id="info_cambio_numeros" style="display: none;">
                                <i class="ri-information-line fs-4"></i>
                                <div>
                                    <strong>Cambio en numeración detectado:</strong>
                                    <span id="texto_cambio_numeros"></span>
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="regenerar_numeros" name="regenerar_numeros">
                                <label class="form-check-label" for="regenerar_numeros">
                                    <strong>Regenerar completamente</strong> (elimina todos los números y crea nuevos - solo si NO hay tickets vendidos)
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="ri-lightbulb-line"></i> Si solo cambias el rango (ej: de 1-20 a 1-30), los números nuevos se agregarán automáticamente sin necesidad de marcar esta opción.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn_guardar_rifa">
                        <i class="ri-save-line me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de gestión de premios asociados a una rifa -->
<div class="modal fade" id="modal_premios_rifa" tabindex="-1" aria-labelledby="modal_premios_rifa_label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_premios_rifa_label">
                    <i class="ri-gift-line me-2"></i>Premios del sorteo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="premios_rifa_id_hidden">
                <div class="alert alert-soft-primary d-flex align-items-center gap-2" role="alert">
                    <i class="ri-information-line fs-4"></i>
                    <div>
                        <strong>Rifa seleccionada:</strong>
                        <span id="premios_rifa_nombre" class="fw-semibold"></span>
                    </div>
                </div>
                <div class="alert alert-soft-warning d-flex align-items-start gap-2 d-none" id="alerta_sin_premios_activos" role="alert">
                    <i class="ri-error-warning-line fs-4"></i>
                    <div>
                        No hay premios activos disponibles en la sede. Registra un premio en el módulo de premios para poder asociarlo a esta rifa.
                    </div>
                </div>

                <form id="form_premio_rifa" class="border rounded p-3 mb-3" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Premio <span class="text-danger">*</span></label>
                            <select id="premio_rifa_select" class="form-select" required>
                                <option value="">Seleccionar premio</option>
                            </select>
                            <div class="invalid-feedback">Seleccione un premio</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Orden</label>
                            <input type="number" class="form-control" id="premio_rifa_orden" min="1" placeholder="1">
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="premio_rifa_principal">
                                <label class="form-check-label" for="premio_rifa_principal">Premio principal</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="premio_rifa_cantidad" min="1" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Valor estimado (S/.)</label>
                            <input type="number" class="form-control" id="premio_rifa_valor" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select id="premio_rifa_estado" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Título personalizado</label>
                            <input type="text" id="premio_rifa_titulo" class="form-control" placeholder="Ej: Segundo premio">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea id="premio_rifa_descripcion" class="form-control" rows="2"
                                placeholder="Descripción breve del premio dentro del sorteo"></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light" id="btn_cancelar_premio_rifa">
                            <i class="ri-close-line me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn_guardar_premio_rifa">
                            <i class="ri-save-line me-1"></i>Guardar premio
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="tabla_premios_rifa" class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Premio</th>
                                <th class="text-center">Orden</th>
                                <th class="text-center">Principal</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

