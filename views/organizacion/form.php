<form id="form_organizacion" novalidate>
    <input type="hidden" id="organizacion_id" name="organizacion_id">

    <div class="row g-3">
        <div class="col-md-4">
            <label for="organizacion_codigo" class="form-label">Código <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="organizacion_codigo" name="organizacion_codigo"
                placeholder="Ejemplo: SEDE01" required>
            <div class="invalid-feedback" id="organizacion_codigo_error"></div>
        </div>
        <div class="col-md-4">
            <label for="organizacion_nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="organizacion_nombre" name="organizacion_nombre"
                placeholder="Nombre de la sede" required>
            <div class="invalid-feedback" id="organizacion_nombre_error"></div>
        </div>
        <div class="col-md-4">
            <label for="organizacion_pais" class="form-label">País</label>
            <input type="text" class="form-control" id="organizacion_pais" name="organizacion_pais"
                placeholder="Ejemplo: Perú">
            <div class="invalid-feedback" id="organizacion_pais_error"></div>
        </div>

        <div class="col-12">
            <label for="organizacion_descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="organizacion_descripcion" name="organizacion_descripcion"
                rows="2" placeholder="Descripción de la organización"></textarea>
        </div>

        <div class="col-md-6">
            <label for="organizacion_direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="organizacion_direccion" name="organizacion_direccion"
                placeholder="Dirección física">
        </div>
        <div class="col-md-3">
            <label for="organizacion_telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="organizacion_telefono" name="organizacion_telefono"
                placeholder="Teléfono">
        </div>
        <div class="col-md-3">
            <label for="organizacion_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="organizacion_email" name="organizacion_email"
                placeholder="correo@sede.com">
        </div>

        <div class="col-md-4">
            <label for="organizacion_moneda" class="form-label">Moneda</label>
            <input type="text" class="form-control" id="organizacion_moneda" name="organizacion_moneda"
                placeholder="Soles">
        </div>
        <div class="col-md-2">
            <label for="organizacion_simbolo_moneda" class="form-label">Símbolo</label>
            <input type="text" class="form-control" id="organizacion_simbolo_moneda" name="organizacion_simbolo_moneda"
                placeholder="S/.">
        </div>
        <div class="col-md-2">
            <label for="organizacion_codigo_moneda" class="form-label">Código ISO</label>
            <input type="text" class="form-control" id="organizacion_codigo_moneda" name="organizacion_codigo_moneda"
                placeholder="PEN">
        </div>
        <div class="col-md-4">
            <label for="organizacion_zona_horaria" class="form-label">Zona horaria</label>
            <input type="text" class="form-control" id="organizacion_zona_horaria" name="organizacion_zona_horaria"
                placeholder="America/Lima">
        </div>

        <div class="col-md-4">
            <label for="organizacion_url_logo" class="form-label">URL Logo</label>
            <input type="text" class="form-control" id="organizacion_url_logo" name="organizacion_url_logo"
                placeholder="URL del logo">
        </div>
        <div class="col-md-4">
            <label for="organizacion_url_landing" class="form-label">URL Landing</label>
            <input type="text" class="form-control" id="organizacion_url_landing" name="organizacion_url_landing"
                placeholder="URL de la landing">
        </div>
        <div class="col-md-2">
            <label for="organizacion_dias_validez_ticket" class="form-label">Días validez ticket</label>
            <input type="number" class="form-control" id="organizacion_dias_validez_ticket" name="organizacion_dias_validez_ticket"
                min="1" value="90">
        </div>
        <div class="col-md-2">
            <label for="organizacion_estado" class="form-label">Estado</label>
            <select class="form-select" id="organizacion_estado" name="organizacion_estado">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary" id="btn_guardar_organizacion">
                <i class="ri-save-line me-1"></i>Guardar cambios
            </button>
        </div>
    </div>
</form>
