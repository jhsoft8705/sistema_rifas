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
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-user-add-line me-2"></i>Formulario de Registro de Empleado
                                    </h5>
                                </div>
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

                                        <!-- Fila 1: Nombres Completos -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="nombre" class="form-label">Nombres<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                                        placeholder="Ingrese los nombres" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="nombre_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="apellido_paterno" class="form-label">Apellido Paterno
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="apellido_paterno"
                                                        name="apellido_paterno"
                                                        placeholder="Ingrese el apellido paterno" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="apellido_paterno_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="apellido_materno" class="form-label">Apellido
                                                        Materno</label>
                                                    <input type="text" class="form-control" id="apellido_materno"
                                                        name="apellido_materno"
                                                        placeholder="Ingrese el apellido materno"
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="apellido_materno_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fila 2: Documento de Identidad -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="tipo_documento" class="form-label">Tipo de Documento
                                                        <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="tipo_documento"
                                                        name="tipo_documento" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione tipo de documento</option>
                                                        <option value="1">DNI</option>
                                                        <option value="2">Carnet de Extranjería</option>
                                                        <option value="3">Pasaporte</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="tipo_documento_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="nro_documento" class="form-label">N° de Documento <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nro_documento"
                                                        name="nro_documento"
                                                        placeholder="Ingrese el número de documento" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="nro_documento_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="fecha_nacimiento" class="form-label">Fecha de
                                                        Nacimiento</label>
                                                    <input type="date" class="form-control" id="fecha_nacimiento"
                                                        name="fecha_nacimiento"
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="fecha_nacimiento_error"></div>
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione sexo</option>
                                                        <option value="m">Hombre</option>
                                                        <option value="f">Mujer</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="sexo_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="estado_civil" class="form-label">Estado Civil<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="estado_civil" name="estado_civil"
                                                        required style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione un estado Civil</option>
                                                        <option value="S">Solter@</option>
                                                        <option value="C">Casad@</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="estado_civil_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="telefono" class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control" id="telefono"
                                                        name="telefono" placeholder="Ingrese el teléfono"
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="telefono_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fila 4: Datos Académicos y Contacto -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="profesion" class="form-label">Profesión<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="profesion" name="profesion" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione profesión</option>
                                                        <option value="1">Ingeniero de Sistemas</option>
                                                        <option value="2">Ingeniero Industrial</option>
                                                        <option value="3">Contador Público</option>
                                                        <option value="4">Administrador de Empresas</option>
                                                        <option value="5">Licenciado en Marketing</option>
                                                        <option value="6">Licenciado en Recursos Humanos</option>
                                                        <option value="7">Economista</option>
                                                        <option value="8">Abogado</option>
                                                        <option value="9">Diseñador Gráfico</option>
                                                        <option value="10">Técnico en Computación</option>
                                                        <option value="11">Otro</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="profesion_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="grado_institucion" class="form-label">Grado Institución<span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="grado_institucion" name="grado_institucion"
                                                        required style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione grado institución</option>
                                                        <option value="1">Secundaria Completa</option>
                                                        <option value="2">Técnico Básico</option>
                                                        <option value="3">Técnico Superior</option>
                                                        <option value="4">Universitario en Curso</option>
                                                        <option value="5">Bachiller</option>
                                                        <option value="6">Título Profesional</option>
                                                        <option value="7">Maestría</option>
                                                        <option value="8">Doctorado</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="grado_institucion_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email <span
                                                            class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="correo@ejemplo.com" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="email_error"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fila 5: Estado y Foto del Empleado -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="estado" class="form-label">Estado <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="estado" name="estado" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione un estado</option>
                                                        <option value="1">Activo</option>
                                                        <option value="0">Inactivo</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="estado_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
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
                                                                style="min-height: 45px; font-size: 1rem;">
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="fecha_ingreso_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="fecha_cese" class="form-label">Fecha de Cese</label>
                                                    <input type="date" class="form-control" id="fecha_cese"
                                                        name="fecha_cese" style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="fecha_cese_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="unidad_organizacional" class="form-label">Unidad
                                                        Organizacional <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="unidad_organizacional"
                                                        name="unidad_organizacional" required
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione unidad organizacional</option>
                                                        <option value="1">Gerencia General</option>
                                                        <option value="2">Recursos Humanos</option>
                                                        <option value="3">Contabilidad</option>
                                                        <option value="4">Ventas</option>
                                                        <option value="5">Marketing</option>
                                                        <option value="6">Tecnología</option>
                                                        <option value="7">Operaciones</option>
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione un cargo</option>
                                                        <option value="1">Gerente General</option>
                                                        <option value="2">Desarrollador Senior</option>
                                                        <option value="3">Analista de Recursos Humanos</option>
                                                        <option value="4">Contador</option>
                                                        <option value="5">Ejecutivo de Ventas</option>
                                                        <option value="6">Especialista en Marketing</option>
                                                        <option value="7">Supervisor de Operaciones</option>
                                                        <option value="8">Asistente Administrativo</option>
                                                        <option value="9">Diseñador Gráfico</option>
                                                        <option value="10">Técnico de Sistemas</option>
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione régimen laboral</option>
                                                        <option value="1">Contrato Indefinido</option>
                                                        <option value="2">Contrato Temporal</option>
                                                        <option value="3">Contrato por Obra</option>
                                                        <option value="4">Contrato de Práctica</option>
                                                        <option value="5">Contrato de Locación de Servicios</option>
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione tipo de trabajador</option>
                                                        <option value="1">Empleado</option>
                                                        <option value="2">Funcionario</option>
                                                        <option value="3">Contratado</option>
                                                        <option value="4">CAS</option>
                                                        <option value="5">Obrero</option>
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione nivel remunerativo</option>
                                                        <option value="1">Nivel I</option>
                                                        <option value="2">Nivel II</option>
                                                        <option value="3">Nivel III</option>
                                                        <option value="4">Nivel IV</option>
                                                        <option value="5">Nivel V</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="nivel_remunerativo_error"></div>
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
                                                                style="min-height: 45px; font-size: 1rem;">
                                                                <option value="">Seleccione sistema de pensión</option>
                                                                <option value="1">ONP</option>
                                                                <option value="2">AFP Integra</option>
                                                                <option value="3">AFP Prima</option>
                                                                <option value="4">AFP Profuturo</option>
                                                                <option value="5">AFP Habitat</option>
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                        <option value="">Seleccione un banco</option>
                                                        <option value="1">Banco de Crédito del Perú</option>
                                                        <option value="2">Banco Interbank</option>
                                                        <option value="3">Banco BBVA</option>
                                                        <option value="4">Banco Scotiabank</option>
                                                        <option value="5">Banco Pichincha</option>
                                                        <option value="6">Banco de la Nación</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="banco_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="numero_cuenta" class="form-label">N° de Cuenta</label>
                                                    <input type="text" class="form-control" id="numero_cuenta"
                                                        name="numero_cuenta" placeholder="Ingrese el número de cuenta"
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="numero_cuenta_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="numero_cci" class="form-label">N° CCI</label>
                                                    <input type="text" class="form-control" id="numero_cci"
                                                        name="numero_cci" placeholder="Ingrese el número CCI"
                                                        style="min-height: 45px; font-size: 1rem;">
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
                                                                                <option value="">Seleccione ubigeo
                                                                                    (opcional)</option>
                                                                                <option value="1">Lima - Lima -
                                                                                    Miraflores</option>
                                                                                <option value="2">Lima - Lima - San
                                                                                    Isidro</option>
                                                                                <option value="3">Lima - Lima - Surco
                                                                                </option>
                                                                                <option value="4">Lima - Lima - La
                                                                                    Molina</option>
                                                                                <option value="5">Lima - Lima - San
                                                                                    Borja</option>
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
                                                                                <option value="1">Lima - Lima -
                                                                                    Miraflores</option>
                                                                                <option value="2">Lima - Lima - San
                                                                                    Isidro</option>
                                                                                <option value="3">Lima - Lima - Surco
                                                                                </option>
                                                                                <option value="4">Lima - Lima - La
                                                                                    Molina</option>
                                                                                <option value="5">Lima - Lima - San
                                                                                    Borja</option>
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
                                                                                <option value="">Seleccione ubigeo
                                                                                    (opcional)</option>
                                                                                <option value="1">Lima - Lima -
                                                                                    Miraflores</option>
                                                                                <option value="2">Lima - Lima - San
                                                                                    Isidro</option>
                                                                                <option value="3">Lima - Lima - Surco
                                                                                </option>
                                                                                <option value="4">Lima - Lima - La
                                                                                    Molina</option>
                                                                                <option value="5">Lima - Lima - San
                                                                                    Borja</option>
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
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="cuspp_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="airhsp" class="form-label">AIRHSP</label>
                                                    <input type="text" class="form-control" id="airhsp" name="airhsp"
                                                        placeholder="Ingrese el AIRHSP"
                                                        style="min-height: 45px; font-size: 1rem;">
                                                    <div class="invalid-feedback" id="airhsp_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="codigo_reloj" class="form-label">Código Reloj</label>
                                                    <input type="text" class="form-control" id="codigo_reloj"
                                                        name="codigo_reloj" placeholder="Ingrese el código del reloj"
                                                        style="min-height: 45px; font-size: 1rem;">
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
                                                        style="min-height: 80px; font-size: 1rem;"></textarea>
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

    <?php require_once __DIR__ . '/../../components/js.php' ?>
    <script src="<?= Enrutamiento::dominio() ?>/views/empleados/register/register.js"></script>
</body>

</html>