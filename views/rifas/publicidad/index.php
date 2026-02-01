<!doctype html>
<html lang="es" data-layout="horizontal" data-topbar="light" data-sidebar="light" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Imprimir en Publicidad | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Exportar números disponibles para publicidad en hoja A4 o redes sociales" name="description" />
    <?php require_once __DIR__ . "/../../components/head.php"; ?>
    <style>
        .publicidad-preview { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.375rem; }
        .ticket-numero-publicidad { display: inline-flex; align-items: center; justify-content: center; min-width: 3rem; padding: 0.5rem 0.75rem; margin: 0.25rem; border: 2px solid #0d6efd; border-radius: 0.5rem; font-weight: 700; font-size: 1rem; background: #fff; }
        .formato-a4 { max-width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        /* Vista previa redes: colores del landing (rojo, dorado, negro) */
        .formato-redes { width: 360px; height: 660px; margin: 0 auto; background: linear-gradient(180deg, #f5f5f5 0%, #ffffff 50%, #fffbf8 100%); color: #1a1a1a; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 3px solid #DC143C; }
        .formato-redes .ticket-numero-publicidad { border-color: #DC143C; color: #1a1a1a; background: #fff; }
        .formato-redes #preview_redes_titulo { color: #DC143C; font-weight: 700; }
        .formato-redes .redes-descripcion { color: #1a1a1a; }
        .formato-redes .redes-subtitulo { font-size: 0.85rem; color: #B22222; font-weight: 600; }
        .formato-redes .redes-cta { font-size: 0.8rem; margin-top: 1rem; padding-top: 0.75rem; border-top: 2px solid rgba(220,20,60,0.2); color: #DAA520; font-weight: 600; }
        #canvas-redes { display: none; }
    </style>
</head>

<body>
    <div id="layout-wrapper">
        <?php require_once __DIR__ . "/../../components/navbar.php"; ?>
        <?php require_once __DIR__ . "/../../components/appmenu.php"; ?>

        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">
                                    <i class="ri-megaphone-line me-2"></i>Imprimir en Publicidad
                                </h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio() ?>/admin-dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio() ?>/admin-rifas">Rifas</a></li>
                                        <li class="breadcrumb-item active">Imprimir en publicidad</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sin rifa seleccionada -->
                    <div id="bloque_sin_rifa" class="row d-none">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="ri-error-warning-line text-warning" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">No se ha seleccionado una rifa</h5>
                                    <p class="text-muted">Utiliza el botón "Imprimir en publicidad" desde la lista de rifas para ver los números disponibles.</p>
                                    <a href="<?= Enrutamiento::dominio() ?>/admin-rifas" class="btn btn-primary">
                                        <i class="ri-arrow-left-line me-1"></i>Volver a Rifas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido con rifa -->
                    <div id="bloque_con_rifa" class="row d-none">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-ticket-2-line me-2"></i>
                                        <span id="publicidad_rifa_nombre">—</span>
                                    </h5>
                                    <p class="text-muted mb-0 mt-1 small">
                                        <span id="publicidad_total_numeros">0</span> números disponibles para publicidad
                                    </p>
                                </div>
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-a4" role="tab">
                                                <i class="ri-file-text-line me-1"></i> Hoja A4
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tab-redes" role="tab">
                                                <i class="ri-image-line me-1"></i> Redes sociales
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <!-- Tab A4 -->
                                        <div class="tab-pane fade show active" id="tab-a4" role="tabpanel">
                                            <div class="row mb-3 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label">Tickets por hoja</label>
                                                    <select id="a4_por_hoja" class="form-select">
                                                        <option value="10">10 por hoja</option>
                                                        <option value="20">20 por hoja</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label d-block">Página</label>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <button type="button" id="a4_prev_page" class="btn btn-outline-secondary btn-sm" title="Página anterior"><i class="ri-arrow-left-s-line"></i></button>
                                                        <span id="a4_pagina_info" class="small text-muted">Página 1 de 1</span>
                                                        <button type="button" id="a4_next_page" class="btn btn-outline-secondary btn-sm" title="Página siguiente"><i class="ri-arrow-right-s-line"></i></button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <button type="button" id="btn_exportar_pdf" class="btn btn-success">
                                                        <i class="ri-file-pdf-line me-1"></i>Exportar PDF (esta página)
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="publicidad-preview p-3 overflow-auto" style="max-height: 70vh;">
                                                <div id="preview_a4" class="formato-a4">
                                                    <h6 class="text-center mb-3" id="preview_a4_titulo">Números disponibles</h6>
                                                    <div id="preview_a4_numeros" class="d-flex flex-wrap justify-content-center"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tab Redes -->
                                        <div class="tab-pane fade" id="tab-redes" role="tabpanel">
                                            <div class="row mb-3 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label">Tickets por imagen</label>
                                                    <select id="redes_por_imagen" class="form-select">
                                                        <option value="10">10 por imagen</option>
                                                        <option value="8">8 por imagen</option>
                                                        <option value="12">12 por imagen</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label d-block">Página / Rango</label>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <button type="button" id="redes_prev_page" class="btn btn-outline-secondary btn-sm" title="Anterior"><i class="ri-arrow-left-s-line"></i></button>
                                                        <span id="redes_pagina_info" class="small text-muted">Página 1 de 1</span>
                                                        <button type="button" id="redes_next_page" class="btn btn-outline-secondary btn-sm" title="Siguiente"><i class="ri-arrow-right-s-line"></i></button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <button type="button" id="btn_exportar_imagen" class="btn btn-primary">
                                                        <i class="ri-image-download-line me-1"></i>Exportar imagen
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-muted small">Imagen vertical 1080×1980 px para stories (Instagram, Facebook).</p>
                                            <div class="publicidad-preview p-3 overflow-auto d-flex justify-content-center">
                                                <div id="preview_redes" class="formato-redes">
                                                    <h6 class="text-center mb-1" id="preview_redes_titulo">Números disponibles</h6>
                                                    <p class="text-center redes-descripcion mb-2 small" id="preview_redes_descripcion" style="font-size:0.8rem;opacity:0.9;"></p>
                                                    <p class="text-center redes-subtitulo mb-2" id="preview_redes_subtitulo">Elige tu número y participa</p>
                                                    <div id="preview_redes_numeros" class="d-flex flex-wrap justify-content-center"></div>
                                                    <p class="text-center redes-cta mb-0" id="preview_redes_cta">¡Reserva el tuyo! · Sorteo 100% transparente</p>
                                                </div>
                                            </div>
                                            <canvas id="canvas-redes"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once __DIR__ . '/../../components/footer.php'; ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/../../components/js.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="<?= Enrutamiento::dominio() ?>/views/rifas/publicidad/publicidad.js"></script>
</body>

</html>
