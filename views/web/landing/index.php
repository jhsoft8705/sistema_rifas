<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Participa en la Rifa | Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Participa en nuestros sorteos y rifas" name="description" />
    <meta content="Sistema de Rifas" name="author" />
    <?php
    $base_project = basename(dirname(dirname(dirname(__DIR__))));
    $base_url = '/' . $base_project;

    require_once __DIR__ . '/../../config/conexion.php';
    require_once __DIR__ . '/../../models/Rifa.php';

    $sedeId = isset($_GET['sede']) ? (int) $_GET['sede'] : 1;
    $rifaModel = new Rifa();
    $rifasResponse = $rifaModel->listar_rifas($sedeId, 'EN_VENTA');
    $rifasData = ($rifasResponse['ok'] ?? false) ? ($rifasResponse['data'] ?? []) : [];
    ?>

    <link rel="shortcut icon" href="<?= $base_url ?>/assets/images/favicon.ico">
    <script src="<?= $base_url ?>/assets/js/layout.js"></script>
    <link href="<?= $base_url ?>/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base_url ?>/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base_url ?>/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base_url ?>/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
    <style>
        .rifa-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .rifa-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .grid-number {
            min-width: 80px;
            min-height: 80px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f5f7fb;
            border: 1px solid #d7def0;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }
        .grid-number.selected {
            background: #405189;
            color: #fff;
            border-color: #223464;
        }
        .grid-number.sold {
            background: #e8eaef;
            color: #6c757d;
            border-style: dashed;
            cursor: not-allowed;
        }
        .selected-list span {
            background: rgba(64,81,137,0.1);
            color: #405189;
            border-radius: 6px;
            padding: 6px 10px;
            margin-right: 6px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }
    </style>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <div class="layout-wrapper landing">
        <header class="bg-light py-4">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h1 class="h3 mb-1">Participa en nuestros sorteos</h1>
                        <p class="text-muted mb-0">Selecciona tu número favorito y completa tu registro en minutos.</p>
                    </div>
                    <a href="<?= $base_url ?>" class="btn btn-outline-primary">
                        <i class="ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
            </div>
        </header>

        <main class="py-5">
            <div class="container">
                <section class="mb-5">
                    <div class="row g-4" id="rifas_list">
                        <?php if (empty($rifasData)): ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="ri-information-line me-2"></i>Por el momento no hay rifas disponibles para la venta.
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($rifasData as $rifa): ?>
                                <div class="col-md-4">
                                    <div class="card rifa-card" data-rifa='<?= json_encode($rifa) ?>'>
                                        <div class="card-body">
                                            <h5 class="card-title mb-2"><?= htmlspecialchars($rifa['nombre']) ?></h5>
                                            <p class="text-muted mb-3"><?= htmlspecialchars($rifa['descripcion'] ?? 'Sin descripción') ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-uppercase text-muted">Precio ticket</small>
                                                    <h4 class="mb-0 text-primary">S/ <?= number_format($rifa['precio_ticket'], 2) ?></h4>
                                                </div>
                                                <button class="btn btn-primary btn-sm seleccionar-rifa">Ver números</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <section id="section_numeros" class="d-none">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="ri-grid-line me-2"></i><span id="rifa_nombre_display">Selecciona una rifa</span></h5>
                                    <span class="badge bg-secondary" id="rifa_precio_display">S/ 0.00</span>
                                </div>
                                <div class="card-body">
                                    <div id="grid_numbers" class="d-flex flex-wrap gap-2"></div>
                                    <div id="grid_placeholder" class="text-center text-muted py-4">
                                        <i class="ri-grid-line display-6 d-block mb-2"></i>
                                        Selecciona una rifa para mostrar sus números disponibles.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Resumen</h5>
                                    <p class="text-muted small">Selecciona los números que deseas comprar y completa tus datos.</p>

                                    <div class="mb-3">
                                        <label class="form-label">Números seleccionados</label>
                                        <div class="selected-list border rounded p-2" id="selected_numbers_list">
                                            <span class="text-muted">Ninguno</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Total a pagar</label>
                                        <input type="text" id="total_pago" class="form-control" placeholder="S/ 0.00" readonly>
                                    </div>

                                    <form id="form_cliente" novalidate>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre completo</label>
                                            <input type="text" class="form-control" id="cliente_nombre" required>
                                            <div class="invalid-feedback">Ingresa tu nombre.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Correo electrónico</label>
                                            <input type="email" class="form-control" id="cliente_email" required>
                                            <div class="invalid-feedback">Ingresa un correo válido.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Teléfono / WhatsApp</label>
                                            <input type="text" class="form-control" id="cliente_telefono" required>
                                            <div class="invalid-feedback">Ingresa tu número de contacto.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Comentarios</label>
                                            <textarea class="form-control" id="cliente_comentarios" rows="2" placeholder="Información adicional (opcional)"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="ri-shopping-cart-2-line me-1"></i> Reservar número(s)
                                        </button>
                                    </form>
                                    <small class="text-muted d-block mt-3">Te contactaremos para finalizar el pago y confirmar tu participación.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <footer class="custom-footer bg-dark py-4 mt-5">
            <div class="container text-center">
                <p class="copy-rights mb-0 text-white-50">
                    <script> document.write(new Date().getFullYear())</script> © Sistema de Rifas - Todos los derechos reservados
                </p>
            </div>
        </footer>
    </div>

    <script>
        window.LANDING_RIFAS = <?= json_encode($rifasData); ?>;
    </script>

    <script src="<?= $base_url ?>/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base_url ?>/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= $base_url ?>/assets/libs/node-waves/waves.min.js"></script>
    <script src="<?= $base_url ?>/assets/libs/feather-icons/feather.min.js"></script>
    <script src="<?= $base_url ?>/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="<?= $base_url ?>/assets/js/plugins.js"></script>
    <script src="landing.js"></script>
</body>

</html>


