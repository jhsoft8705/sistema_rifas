<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Registro de Empleados | Control de Asistencia CAFED</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de Control de Asistencia CAFED" name="Registro de empleados" />
    <meta content="Cafed" name="Team Otic Cafed" />
    <?php require_once __DIR__ . "/../../components/head.php"; ?>
</head>

<body>
    <div id="layout-wrapper">
        <?php require_once __DIR__ . "/../../components/navbar.php"; ?>
        <?php require_once __DIR__ . "/../../components/appmenu.php"; ?>
        <div class="vertical-overlay"></div>
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Registro de Empleados</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a
                                                href="<?= Enrutamiento::dominio() ?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a
                                                href="<?= Enrutamiento::dominio() ?>/empleados">Empleados</a></li>
                                        <li class="breadcrumb-item active">Registro</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Registro -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                              <!--   <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-user-add-line me-2"></i>Formulario de Registro de Empleado
                                    </h5>
                                </div> -->
                                <div class="card-body">
                                    <form id="form_empleado" novalidate>
                                        <input type="hidden" id="empleado_id" name="empleado_id">

                                        <!-- Datos Personales -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">
                                                    <i class="ri-user-3-line me-2"></i>Datos Personales
                                                </h6>
                                            </div>
                                        </div>

                                        <!-- Fila 1: Documento de Identidad (PRIMERO) -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="tipo_documento" class="form-label">Tipo de Documento
                                                        <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="tipo_documento"
                                                        name="tipo_documento" required>
                                                        <option value="">Seleccione tipo de documento</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="tipo_documento_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="nro_documento" class="form-label">N° de Documento <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="nro_documento"
                                                            name="nro_documento"
                                                            placeholder="Ingrese el número de documento" required>
                                                        <button class="btn btn-primary" type="button" id="btn_consultar_reniec"
                                                            title="Consultar datos en RENIEC/SUNAT">
                                                            <i class="ri-search-line"></i> Consultar
                                                        </button>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="ri-information-line"></i> Consulta automática de datos desde RENIEC/SUNAT
                                                    </small>
                                                    <div class="invalid-feedback" id="nro_documento_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="ruc" class="form-label">RUC <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="ruc"
                                                        name="ruc" placeholder="Ingrese el RUC" required
                                                        maxlength="11">
                                                    <div class="invalid-feedback" id="ruc_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="fecha_nacimiento" class="form-label">Fecha de
                                                        Nacimiento</label>
                                                    <input type="date" class="form-control" id="fecha_nacimiento"
                                                        name="fecha_nacimiento">
                                                    <div class="invalid-feedback" id="fecha_nacimiento_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fila 2: Nombres Completos (SE LLENAN AUTOMÁTICAMENTE DESPUÉS DE CONSULTA) -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="nombre" class="form-label">Nombres<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                                        placeholder="Ingrese los nombres" required>
                                                    <div class="invalid-feedback" id="nombre_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="apellido_paterno" class="form-label">Apellido Paterno
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="apellido_paterno"
                                                        name="apellido_paterno"
                                                        placeholder="Ingrese el apellido paterno" required>
                                                    <div class="invalid-feedback" id="apellido_paterno_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="apellido_materno" class="form-label">Apellido
                                                        Materno</label>
                                                    <input type="text" class="form-control" id="apellido_materno"
                                                        name="apellido_materno"
                                                        placeholder="Ingrese el apellido materno">
                                                    <div class="invalid-feedback" id="apellido_materno_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fila 3: Datos Personales Básicos -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="sexo" class="form-label">Sexo<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="sexo" name="sexo" required
>
                                                        <option value="">Seleccione sexo</option>
                                                        <option value="m">Hombre</option>
                                                        <option value="f">Mujer</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="sexo_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="estado_civil" class="form-label">Estado Civil</label>
                                                    <select class="form-select" id="estado_civil" name="estado_civil"
>
                                                        <option value="">Seleccione un estado Civil</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="estado_civil_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="telefono" class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control" id="telefono"
                                                        name="telefono" placeholder="Ingrese el teléfono"
>
                                                    <div class="invalid-feedback" id="telefono_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fila 4: Datos Académicos y Contacto -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="profesion" class="form-label">Profesión</label>
                                                    <select class="form-select" id="profesion" name="profesion"
>
                                                        <option value="">Seleccione profesión</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="profesion_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="grado_institucion" class="form-label">Grado Institución</label>
                                                    <select class="form-select" id="grado_institucion" name="grado_institucion"
>
                                                        <option value="">Seleccione grado institución</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="grado_institucion_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="juan.perez@gmail.com"
>
                                                    <div class="invalid-feedback" id="email_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fila 5: Foto del Empleado -->
                                        <div class="row">
                                            <!-- Estado siempre será Activo por defecto en registro -->
                                            <input type="hidden" id="estado" name="estado" value="1">
                                            
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="foto_empleado" class="form-label">Foto del Trabajador</label>
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar-lg"
                                                                style="border: 2px dashed #ccc; border-radius: 8px; overflow: hidden; width: 120px; height: 120px;">
                                                                <img id="preview_foto"
                                                                    src="<?= Enrutamiento::dominio() ?>/assets/images/users/user-dummy-img.jpg"
                                                                    alt="Foto del empleado" class="img-thumbnail"
                                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <input type="file" class="form-control" id="foto_empleado"
                                                                name="foto_empleado" accept="image/*"
>
                                                            <small class="text-muted">
                                                                <i class="ri-information-line me-1"></i>
                                                                Formatos permitidos: JPG, PNG, JPEG. Tamaño máximo: 2MB
                                                            </small>
                                                            <div class="invalid-feedback" id="foto_empleado_error">
                                                            </div>
                                                            <button type="button" class="btn btn-sm btn-danger mt-2"
                                                                id="btn_eliminar_foto" style="display: none;">
                                                                <i class="ri-delete-bin-line me-1"></i>Eliminar Foto
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Datos Laborales -->
                                        <div class="row mb-4 mt-4">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">
                                                    <i class="ri-briefcase-line me-2"></i>Datos Laborales
                                                </h6>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="fecha_ingreso" class="form-label">Fecha de Ingreso <span
                                                            class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="fecha_ingreso"
                                                        name="fecha_ingreso" required
>
                                                    <div class="invalid-feedback" id="fecha_ingreso_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="fecha_cese" class="form-label">Fecha de Cese</label>
                                                    <input type="date" class="form-control" id="fecha_cese"
                                                        name="fecha_cese" >
                                                    <div class="invalid-feedback" id="fecha_cese_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="unidad_organizacional" class="form-label">Unidad
                                                        Organizacional <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="unidad_organizacional"
                                                        name="unidad_organizacional" required
>
                                                        <option value="">Seleccione unidad organizacional</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="unidad_organizacional_error">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="cargo" class="form-label">Cargo <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="cargo" name="cargo" required
>
                                                        <option value="">Seleccione un cargo</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="cargo_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="regimen_laboral" class="form-label">Régimen / Condición
                                                        Laboral <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="regimen_laboral"
                                                        name="regimen_laboral" required
>
                                                        <option value="">Seleccione régimen laboral</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="regimen_laboral_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="tipo_trabajador" class="form-label">Tipo de Trabajador
                                                        <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="tipo_trabajador"
                                                        name="tipo_trabajador" required
>
                                                        <option value="">Seleccione tipo de trabajador</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="tipo_trabajador_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="nivel_remunerativo" class="form-label">Nivel
                                                        Remunerativo <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="nivel_remunerativo"
                                                        name="nivel_remunerativo" required
>
                                                        <option value="">Seleccione nivel remunerativo</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="nivel_remunerativo_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="turno" class="form-label">Turno Laboral <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="turno" name="turno" required
>
                                                        <option value="">Seleccione un turno</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="turno_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="tipo_jornada" class="form-label">Tipo de Jornada</label>
                                                    <select class="form-select" id="tipo_jornada" name="tipo_jornada"
>
                                                        <option value="Presencial">Presencial</option>
                                                        <option value="Remoto">Remoto</option>
                                                        <option value="Hibrido">Híbrido</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="tipo_jornada_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Control de Asistencia</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="es_fiscalizado"
                                                               name="es_fiscalizado" checked
                                                               data-bs-toggle="tooltip" data-bs-placement="right"
                                                               title="Activa el control biométrico para este empleado">
                                                        <label class="form-check-label" for="es_fiscalizado">
                                                            Empleado fiscalizado (requiere marcación)
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">Si está desactivado, el empleado no requerirá marcación de asistencia</small>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label class="form-label">Sistema de Pensión</label>
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check form-switch me-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="no_paga_seguro" name="no_paga_seguro"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Marcar si no paga seguro">
                                                            <label class="form-check-label" for="no_paga_seguro">
                                                                No paga seguro
                                                            </label>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <select class="form-select" id="sistema_pension"
                                                                name="sistema_pension"
>
                                                                <option value="">Seleccione sistema de pensión</option> 
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="invalid-feedback" id="sistema_pension_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Datos Bancarios -->
                                        <div class="row mb-4 mt-4">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">
                                                    <i class="ri-bank-line me-2"></i>Datos Bancarios
                                                </h6>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="banco" class="form-label">Banco</label>
                                                    <select class="form-select" id="banco" name="banco"
>
                                                        <option value="">Seleccione un banco</option> 
                                                    </select>
                                                    <div class="invalid-feedback" id="banco_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="numero_cuenta" class="form-label">N° de Cuenta</label>
                                                    <input type="text" class="form-control" id="numero_cuenta"
                                                        name="numero_cuenta" placeholder="Ingrese el número de cuenta"
>
                                                    <div class="invalid-feedback" id="numero_cuenta_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="numero_cci" class="form-label">N° CCI</label>
                                                    <input type="text" class="form-control" id="numero_cci"
                                                        name="numero_cci" placeholder="Ingrese el número CCI"
>
                                                    <div class="invalid-feedback" id="numero_cci_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Direcciones -->
                                        <div class="row mb-4 mt-4">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">
                                                    <i class="ri-map-pin-line me-2"></i>Direcciones
                                                </h6>

                                                <!-- Acordeón de Direcciones -->
                                                <div class="accordion" id="accordionDirecciones">
                                                    <!-- Dirección Actual -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingActual">
                                                            <button class="accordion-button" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseActual" aria-expanded="true"
                                                                aria-controls="collapseActual">
                                                                <i class="ri-home-line me-2 text-primary"></i>Dirección
                                                                Actual <span class="text-danger ms-2">*</span>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseActual"
                                                            class="accordion-collapse collapse show"
                                                            aria-labelledby="headingActual"
                                                            data-bs-parent="#accordionDirecciones">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="direccion_actual"
                                                                                class="form-label">Dirección Completa
                                                                                <span
                                                                                    class="text-danger">*</span></label>
                                                                            <textarea class="form-control"
                                                                                id="direccion_actual"
                                                                                name="direccion_actual" rows="2"
                                                                                placeholder="Ingrese la dirección completa"
                                                                                required></textarea>
                                                                            <div class="invalid-feedback"
                                                                                id="direccion_actual_error"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="referencia_actual"
                                                                                class="form-label">Referencia</label>
                                                                            <input type="text" class="form-control"
                                                                                id="referencia_actual"
                                                                                name="referencia_actual"
                                                                                placeholder="Cerca de... (opcional)">
                                                                            <div class="invalid-feedback"
                                                                                id="referencia_actual_error"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="ubigeo_actual"
                                                                                class="form-label">Ubigeo</label>
                                                                            <select class="form-select"
                                                                                id="ubigeo_actual" name="ubigeo_actual">
                                                                                <!-- <option value="">Seleccione ubigeo (opcional)</option> -->
                                                                          
                                                                            </select>
                                                                            <div class="invalid-feedback"
                                                                                id="ubigeo_actual_error"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <div class="form-check form-switch mt-4">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    id="es_principal_actual"
                                                                                    name="es_principal_actual" checked>
                                                                                <label class="form-check-label"
                                                                                    for="es_principal_actual">
                                                                                    Dirección Principal
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Geolocalización -->
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">
                                                                                <i class="ri-map-pin-2-line me-1"></i>Geolocalización
                                                                            </label>
                                                                            <button type="button" class="btn btn-info btn-sm ms-2" id="btn_buscar_ubicacion">
                                                                                <i class="ri-search-line me-1"></i>Buscar Ubicación en Mapa
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <label for="coordenada_x" class="form-label">Latitud (X)</label>
                                                                            <input type="text" class="form-control" id="coordenada_x"
                                                                                name="coordenada_x" placeholder="-12.0464" readonly>
                                                                            <small class="text-muted">Se completa al buscar en el mapa</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <label for="coordenada_y" class="form-label">Longitud (Y)</label>
                                                                            <input type="text" class="form-control" id="coordenada_y"
                                                                                name="coordenada_y" placeholder="-77.0428" readonly>
                                                                            <small class="text-muted">Se completa al buscar en el mapa</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="mb-3">
                                                                            <label for="url_maps" class="form-label">URL Google Maps</label>
                                                                            <div class="input-group">
                                                                                <input type="url" class="form-control" id="url_maps"
                                                                                    name="url_maps" placeholder="https://maps.google.com/..." readonly>
                                                                                <button class="btn btn-outline-secondary" type="button" id="btn_abrir_mapa"
                                                                                    style="display: none;" title="Abrir en Google Maps">
                                                                                    <i class="ri-external-link-line"></i>
                                                                                </button>
                                                                            </div>
                                                                            <small class="text-muted">Se genera automáticamente</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Dirección RENIEC -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingReniec">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseReniec" aria-expanded="false"
                                                                aria-controls="collapseReniec">
                                                                <i
                                                                    class="ri-file-text-line me-2 text-info"></i>Dirección
                                                                RENIEC
                                                            </button>
                                                        </h2>
                                                        <div id="collapseReniec" class="accordion-collapse collapse"
                                                            aria-labelledby="headingReniec"
                                                            data-bs-parent="#accordionDirecciones">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="direccion_reniec"
                                                                                class="form-label">Dirección
                                                                                Completa</label>
                                                                            <textarea class="form-control"
                                                                                id="direccion_reniec"
                                                                                name="direccion_reniec" rows="2"
                                                                                placeholder="Ingrese la dirección según RENIEC"></textarea>
                                                                            <div class="invalid-feedback"
                                                                                id="direccion_reniec_error"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="referencia_reniec"
                                                                                class="form-label">Referencia</label>
                                                                            <input type="text" class="form-control"
                                                                                id="referencia_reniec"
                                                                                name="referencia_reniec"
                                                                                placeholder="Cerca de... (opcional)">
                                                                            <div class="invalid-feedback"
                                                                                id="referencia_reniec_error"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="ubigeo_reniec"
                                                                                class="form-label">Ubigeo</label>
                                                                            <select class="form-select"
                                                                                id="ubigeo_reniec" name="ubigeo_reniec">
                                                                                <option value="">Seleccione ubigeo
                                                                                    (opcional)</option>
                                                                                 <!-- <option value="1">Lima - Lima - Miraflores</option> -->
                                                                          
                                                                            </select>
                                                                            <div class="invalid-feedback"
                                                                                id="ubigeo_reniec_error"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <div class="form-check form-switch mt-4">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    id="es_principal_reniec"
                                                                                    name="es_principal_reniec">
                                                                                <label class="form-check-label"
                                                                                    for="es_principal_reniec">
                                                                                    Dirección Principal
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Dirección Laboral -->
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingLaboral">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#collapseLaboral" aria-expanded="false"
                                                                aria-controls="collapseLaboral">
                                                                <i
                                                                    class="ri-building-line me-2 text-warning"></i>Dirección
                                                                Laboral
                                                            </button>
                                                        </h2>
                                                        <div id="collapseLaboral" class="accordion-collapse collapse"
                                                            aria-labelledby="headingLaboral"
                                                            data-bs-parent="#accordionDirecciones">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="direccion_laboral"
                                                                                class="form-label">Dirección
                                                                                Completa</label>
                                                                            <textarea class="form-control"
                                                                                id="direccion_laboral"
                                                                                name="direccion_laboral" rows="2"
                                                                                placeholder="Ingrese la dirección laboral"></textarea>
                                                                            <div class="invalid-feedback"
                                                                                id="direccion_laboral_error"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="referencia_laboral"
                                                                                class="form-label">Referencia</label>
                                                                            <input type="text" class="form-control"
                                                                                id="referencia_laboral"
                                                                                name="referencia_laboral"
                                                                                placeholder="Cerca de... (opcional)">
                                                                            <div class="invalid-feedback"
                                                                                id="referencia_laboral_error"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="ubigeo_laboral"
                                                                                class="form-label">Ubigeo</label>
                                                                            <select class="form-select"
                                                                                id="ubigeo_laboral"
                                                                                name="ubigeo_laboral">
                                                                                <option value="">Seleccione ubigeo (opcional)</option>
                                                                                <!-- <option value="1">Lima - Lima - Miraflores</option>-->                                                                   
                                                                            </select>
                                                                            <div class="invalid-feedback"
                                                                                id="ubigeo_laboral_error"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <div class="form-check form-switch mt-4">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    id="es_principal_laboral"
                                                                                    name="es_principal_laboral">
                                                                                <label class="form-check-label"
                                                                                    for="es_principal_laboral">
                                                                                    Dirección Principal
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Datos Adicionales -->
                                        <div class="row mb-4 mt-4">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">
                                                    <i class="ri-information-line me-2"></i>Datos Adicionales
                                                </h6>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="cuspp" class="form-label">CUSPP</label>
                                                    <input type="text" class="form-control" id="cuspp" name="cuspp"
                                                        placeholder="Ingrese el CUSPP"
>
                                                    <div class="invalid-feedback" id="cuspp_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="airhsp" class="form-label">AIRHSP</label>
                                                    <input type="text" class="form-control" id="airhsp" name="airhsp"
                                                        placeholder="Ingrese el AIRHSP"
>
                                                    <div class="invalid-feedback" id="airhsp_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="codigo_reloj" class="form-label">Código Reloj</label>
                                                    <input type="text" class="form-control" id="codigo_reloj"
                                                        name="codigo_reloj" placeholder="Ingrese el código del reloj"
>
                                                    <div class="invalid-feedback" id="codigo_reloj_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label for="observaciones" class="form-label">Observaciones</label>
                                                    <textarea class="form-control" id="observaciones"
                                                        name="observaciones" rows="3"
                                                        placeholder="Ingrese observaciones adicionales"
></textarea>
                                                    <div class="invalid-feedback" id="observaciones_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Botones de Acción -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-light" id="btn_limpiar"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Limpiar formulario">
                                                        <i class="ri-refresh-line me-1"></i>Limpiar
                                                    </button>
                                                    <button type="button" class="btn btn-secondary" id="btn_cancelar"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Cancelar y volver a la lista">
                                                        <i class="ri-arrow-left-line me-1"></i>Volver
                                                    </button>
                                                    <button type="submit" class="btn btn-primary" id="btn_guardar"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Guardar datos del empleado">
                                                        <i class="ri-save-line me-1"></i><span
                                                            id="btn_guardar_text">Guardar Empleado</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once __DIR__ . '/../../components/footer.php' ?>
        </div>
    </div>

    <!-- Modal de Geolocalización -->
    <div class="modal fade" id="modal_mapa" tabindex="-1" aria-labelledby="modal_mapa_label" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_mapa_label">
                        <i class="ri-map-pin-line me-2"></i>Buscar Ubicación en el Mapa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Buscar dirección</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search_address" 
                                placeholder="Ej: Av. Arequipa 1234, Miraflores, Lima">
                            <button class="btn btn-primary" type="button" id="btn_search_address">
                                <i class="ri-search-line"></i> Buscar
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="ri-information-line"></i> También puedes hacer clic directamente en el mapa para seleccionar la ubicación
                        </small>
                    </div>
                    <div id="map" style="height: 500px; border-radius: 8px;"></div>
                    <div class="mt-3">
                        <div class="alert alert-info" role="alert">
                            <i class="ri-information-line me-2"></i>
                            <strong>Ubicación seleccionada:</strong>
                            <div id="selected_location" class="mt-2">
                                <span class="text-muted">Haz clic en el mapa o busca una dirección</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn_confirmar_ubicacion" disabled>
                        <i class="ri-check-line me-1"></i>Confirmar Ubicación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../components/js.php' ?>
    
    <!-- Leaflet CSS y JS para el mapa -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script src="<?= Enrutamiento::dominio() ?>/views/empleados/register/register.js"></script>
</body>

</html>