
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>

    <meta charset="utf-8" />
    <title>Sistema de Rifas - Participa y Gana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema profesional de rifas y sorteos. Participa y gana increíbles premios." name="description" />
    <meta content="Sistema de Rifas" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!--Swiper slider css-->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="navbar">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="index.html">
                    <img src="assets/images/logo-dark.png" class="card-logo card-logo-dark" alt="logo dark" height="17">
                    <img src="assets/images/logo-light.png" class="card-logo card-logo-light" alt="logo light" height="17">
                </a>
                <button class="navbar-toggler py-0 fs-20 text-body" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="mdi mdi-menu"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mt-2 mt-lg-0" id="navbar-example">
                        <li class="nav-item">
                            <a class="nav-link active" href="#hero">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#rifas">Rifas Activas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#premios">Premios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#como-participar">Cómo Participar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#ganadores">Ganadores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#consultar-tickets">Mis Tickets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#faqs">Preguntas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contacto</a>
                        </li>
                    </ul>

                    <div class="">
                        <a href="admin-login" class="btn btn-primary"><i class="ri-admin-line me-1"></i> Panel Administrador</a>
                    </div>
                </div>

            </div>
        </nav>
        <!-- end navbar -->

        <!-- start hero section -->
        <section class="section pb-0 hero-section" id="hero">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-sm-10">
                        <div class="text-center mt-lg-5 pt-5">
                            <h1 class="display-6 fw-semibold mb-3 lh-base">¡Participa en nuestras <span class="text-success">Rifas</span> y Gana Increíbles Premios!</h1>
                            <p class="lead text-muted lh-base">Sistema profesional de rifas y sorteos con total transparencia. Compra tus tickets, sube tu comprobante y participa por premios espectaculares.</p>

                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <a href="#rifas" class="btn btn-primary btn-lg"><i class="ri-ticket-2-line align-middle me-1"></i> Ver Rifas Activas</a>
                                <a href="#como-participar" class="btn btn-success btn-lg"><i class="ri-question-line align-middle me-1"></i> ¿Cómo Participar?</a>
                            </div>

                            <!-- Contador Regresivo Principal -->
                            <div class="mt-5">
                                <div class="card border border-success shadow-lg mx-auto" style="max-width: 600px;">
                                    <div class="card-body text-center py-2">
                                        <h5 class="text-success mb-3"><i class="ri-time-line"></i> Próximo Sorteo: <strong>Rifa iPhone 15 Pro Max</strong></h5>
                                        <div id="countdown-hero" class="d-flex justify-content-center gap-3 mb-3">
                                            <div class="text-center">
                                                <div class="bg-primary-subtle rounded p-3" style="min-width: 80px;">
                                                    <h2 class="mb-0 text-primary fw-bold" id="hero-days">00</h2>
                                                    <small class="text-muted">Días</small>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <div class="bg-primary-subtle rounded p-3" style="min-width: 80px;">
                                                    <h2 class="mb-0 text-primary fw-bold" id="hero-hours">00</h2>
                                                    <small class="text-muted">Horas</small>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <div class="bg-primary-subtle rounded p-3" style="min-width: 80px;">
                                                    <h2 class="mb-0 text-primary fw-bold" id="hero-minutes">00</h2>
                                                    <small class="text-muted">Minutos</small>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <div class="bg-primary-subtle rounded p-3" style="min-width: 80px;">
                                                    <h2 class="mb-0 text-primary fw-bold" id="hero-seconds">00</h2>
                                                    <small class="text-muted">Segundos</small>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mb-0 text-muted"><i class="ri-calendar-event-line"></i> Sorteo: 31 de Diciembre 2025</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4   pt-sm-5 mb-sm-n5 demo-carousel">
                            <div class="demo-img-patten-top d-none d-sm-block">
                                <img src="assets/images/landing/img-pattern.png" class="d-block img-fluid" alt="...">
                            </div>
                            <div class="demo-img-patten-bottom d-none d-sm-block">
                                <img src="assets/images/landing/img-pattern.png" class="d-block img-fluid" alt="...">
                            </div>
                            <div class="carousel slide carousel-fade" data-bs-ride="carousel">
                                <div class="carousel-inner shadow-lg p-2 bg-white rounded">
                                    <div class="carousel-item active" data-bs-interval="2000">
                                        <img src="assets/images/demos/default.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/demos/saas.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/demos/material.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/demos/minimal.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/demos/creative.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/demos/modern.png" class="d-block w-100" alt="...">
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <img src="assets/images/demos/interactive.png" class="d-block w-100" alt="...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
            <div class="position-absolute start-0 end-0 bottom-0 hero-shape-svg">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <g mask="url(&quot;#SvgjsMask1003&quot;)" fill="none">
                        <path d="M 0,118 C 288,98.6 1152,40.4 1440,21L1440 140L0 140z">
                        </path>
                    </g>
                </svg>
            </div>
            <!-- end shape -->
        </section>
        <!-- end hero section -->

        <!-- start stats section -->
        <div class="pt-5 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-5">
                            <h5 class="fs-20">Sistema de rifas <span class="text-primary text-decoration-underline">confiable</span> y transparente</h5>
                            
                            <div class="row text-center gy-4 mt-4">
                                <div class="col-lg-3 col-6">
                                    <div>
                                        <h2 class="mb-2"><span class="counter-value text-success" data-target="150">0</span>+</h2>
                                        <div class="text-muted">Rifas Realizadas</div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div>
                                        <h2 class="mb-2"><span class="counter-value text-success" data-target="5000">0</span>+</h2>
                                        <div class="text-muted">Participantes Felices</div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div>
                                        <h2 class="mb-2"><span class="counter-value text-success" data-target="120">0</span>+</h2>
                                        <div class="text-muted">Ganadores</div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div>
                                        <h2 class="mb-2">$<span class="counter-value text-success" data-target="500">0</span>K+</h2>
                                        <div class="text-muted">En Premios Entregados</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end stats section -->

        <!-- start rifas activas -->
        <section class="section" id="rifas">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold lh-base"><i class="ri-ticket-2-fill text-success"></i> Rifas Activas</h1>
                            <p class="text-muted">Elige tu rifa favorita, compra tus tickets y participa por increíbles premios. ¡La suerte está de tu lado!</p>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row g-4">
                    <!-- Rifa 1 - Example -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card ribbon-box border shadow-none">
                            <div class="card-body">
                                <div class="ribbon ribbon-success ribbon-shape">Activa</div>
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-primary text-white fs-1 rounded shadow-sm">
                                        <i class="ri-smartphone-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-center mb-1">Rifa iPhone 15 Pro Max</h5>
                                <p class="text-muted text-center mb-2">Participa y gana el último iPhone</p>
                                
                                <!-- Premios de la Rifa -->
                                <div class="mb-3">
                                    <div class="text-center mb-2">
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ri-gift-line me-1"></i> 1 Premio
                                        </span>
                                    </div>
                                    <div class="text-start px-3">
                                        <small class="text-dark d-block">
                                            <span class="badge bg-warning text-white me-1">1°</span>iPhone 15 Pro Max 256GB
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Precio ticket</p>
                                        <h5 class="mb-0 text-success">$10.00</h5>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Tickets disponibles</p>
                                        <h5 class="mb-0 text-primary">234/500</h5>
                                    </div>
                                </div>
                                <p class="text-muted text-center mb-2"><i class="ri-calendar-line"></i> Sorteo: 31 Dic 2025</p>
                                
                                <!-- Contador Regresivo de la Rifa -->
                                <div class="card bg-warning-subtle border-0 mb-3">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-center gap-2 countdown-rifa" data-fecha="2025-12-31T20:00:00">
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-days">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">días</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-hours">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">hrs</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-minutes">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">min</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-seconds">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">seg</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 47%" aria-valuenow="47" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success w-100 btn-comprar-ticket" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal_comprar_ticket"
                                            data-rifa-id="1"
                                            data-rifa-nombre="Rifa iPhone 15 Pro Max"
                                            data-rifa-precio="10.00"
                                            data-rifa-disponibles="234"
                                            data-rifa-total="500"
                                            data-rifa-premios='[{"nombre":"iPhone 15 Pro Max 256GB","posicion":1,"imagen":"assets/images/premios/iphone-15-pro-max.jpg","descripcion":"iPhone 15 Pro Max 256GB en color Natural Titanium, incluye cargador y funda protectora"}]'>
                                        <i class="ri-shopping-cart-line me-1"></i> Comprar Tickets
                                    </button>
                                    <button class="btn btn-outline-primary w-100 btn-ver-premios" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal_ver_premios"
                                            data-rifa-id="1"
                                            data-rifa-nombre="Rifa iPhone 15 Pro Max"
                                            data-rifa-premios='[{"nombre":"iPhone 15 Pro Max 256GB","posicion":1,"imagen":"assets/images/premios/iphone-15-pro-max.jpg","descripcion":"iPhone 15 Pro Max 256GB en color Natural Titanium, incluye cargador y funda protectora"}]'>
                                        <i class="ri-image-line me-1"></i> Ver Premios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <!-- Rifa 2 - Example -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card ribbon-box border shadow-none">
                            <div class="card-body">
                                <div class="ribbon ribbon-success ribbon-shape">Activa</div>
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-primary text-white fs-1 rounded shadow-sm">
                                        <i class="ri-car-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-center mb-1">Rifa Automóvil 2025</h5>
                                <p class="text-muted text-center mb-2">Camioneta último modelo</p>
                                
                                <!-- Premios de la Rifa -->
                                <div class="mb-3">
                                    <div class="text-center mb-2">
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ri-gift-line me-1"></i> 2 Premios
                                        </span>
                                    </div>
                                    <div class="text-start px-3">
                                        <small class="text-dark d-block mb-1">
                                            <span class="badge bg-warning text-white me-1">1°</span>Camioneta Toyota Hilux 2025
                                        </small>
                                        <small class="text-dark d-block">
                                            <span class="badge bg-secondary text-white me-1">2°</span>$5,000 en efectivo
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Precio ticket</p>
                                        <h5 class="mb-0 text-success">$25.00</h5>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Tickets disponibles</p>
                                        <h5 class="mb-0 text-primary">850/1000</h5>
                                    </div>
                                </div>
                                <p class="text-muted text-center mb-2"><i class="ri-calendar-line"></i> Sorteo: 15 Ene 2026</p>
                                
                                <!-- Contador Regresivo de la Rifa -->
                                <div class="card bg-warning-subtle border-0 mb-3">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-center gap-2 countdown-rifa" data-fecha="2026-01-15T20:00:00">
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-days">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">días</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-hours">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">hrs</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-minutes">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">min</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-seconds">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">seg</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success w-100 btn-comprar-ticket" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal_comprar_ticket"
                                            data-rifa-id="2"
                                            data-rifa-nombre="Rifa Automóvil 2025"
                                            data-rifa-precio="25.00"
                                            data-rifa-disponibles="850"
                                            data-rifa-total="1000"
                                            data-rifa-premios='[{"nombre":"Camioneta Toyota Hilux 2025","posicion":1,"imagen":"assets/images/premios/toyota-hilux.jpg","descripcion":"Camioneta Toyota Hilux 2025 4x4 Doble Cabina, modelo más reciente, color a elegir"},{"nombre":"$5000 en efectivo","posicion":2,"imagen":"assets/images/premios/efectivo.jpg","descripcion":"$5,000 USD en efectivo como segundo premio"}]'>
                                        <i class="ri-shopping-cart-line me-1"></i> Comprar Tickets
                                    </button>
                                    <button class="btn btn-outline-primary w-100 btn-ver-premios" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal_ver_premios"
                                            data-rifa-id="2"
                                            data-rifa-nombre="Rifa Automóvil 2025"
                                            data-rifa-premios='[{"nombre":"Camioneta Toyota Hilux 2025","posicion":1,"imagen":"assets/images/premios/toyota-hilux.jpg","descripcion":"Camioneta Toyota Hilux 2025 4x4 Doble Cabina, modelo más reciente, color a elegir"},{"nombre":"$5000 en efectivo","posicion":2,"imagen":"assets/images/premios/efectivo.jpg","descripcion":"$5,000 USD en efectivo como segundo premio"}]'>
                                        <i class="ri-image-line me-1"></i> Ver Premios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->

                    <!-- Rifa 3 - Example -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card ribbon-box border shadow-none">
                            <div class="card-body">
                                <div class="ribbon ribbon-success ribbon-shape">Activa</div>
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-primary text-white fs-1 rounded shadow-sm">
                                        <i class="ri-flight-takeoff-line"></i>
                                    </div>
                                </div>
                                <h5 class="text-center mb-1">Rifa Viaje a Europa</h5>
                                <p class="text-muted text-center mb-2">Paquete todo incluido para 2 personas</p>
                                
                                <!-- Premios de la Rifa -->
                                <div class="mb-3">
                                    <div class="text-center mb-2">
                                        <span class="badge bg-info-subtle text-info">
                                            <i class="ri-gift-line me-1"></i> 3 Premios
                                        </span>
                                    </div>
                                    <div class="text-start px-3">
                                        <small class="text-dark d-block mb-1">
                                            <span class="badge bg-warning text-white me-1">1°</span>Viaje a Europa para 2 personas
                                        </small>
                                        <small class="text-dark d-block mb-1">
                                            <span class="badge bg-secondary text-white me-1">2°</span>$2,000 en efectivo
                                        </small>
                                        <small class="text-dark d-block">
                                            <span class="badge bg-dark text-white me-1">3°</span>Set de maletas premium
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Precio ticket</p>
                                        <h5 class="mb-0 text-success">$15.00</h5>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted mb-1">Tickets disponibles</p>
                                        <h5 class="mb-0 text-primary">120/300</h5>
                                    </div>
                                </div>
                                <p class="text-muted text-center mb-2"><i class="ri-calendar-line"></i> Sorteo: 28 Feb 2026</p>
                                
                                <!-- Contador Regresivo de la Rifa -->
                                <div class="card bg-warning-subtle border-0 mb-3">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-center gap-2 countdown-rifa" data-fecha="2026-02-28T20:00:00">
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-days">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">días</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-hours">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">hrs</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-minutes">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">min</small>
                                            </div>
                                            <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                                            <div class="text-center">
                                                <div class="fw-bold text-warning countdown-seconds">00</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">seg</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success w-100 btn-comprar-ticket" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal_comprar_ticket"
                                            data-rifa-id="3"
                                            data-rifa-nombre="Rifa Viaje a Europa"
                                            data-rifa-precio="15.00"
                                            data-rifa-disponibles="120"
                                            data-rifa-total="300"
                                            data-rifa-premios='[{"nombre":"Viaje a Europa para 2 personas","posicion":1,"imagen":"assets/images/premios/viaje-europa.jpg","descripcion":"Viaje todo incluido a Europa para 2 personas, 7 días y 6 noches, incluye vuelos, hotel y desayunos"},{"nombre":"$2000 en efectivo","posicion":2,"imagen":"assets/images/premios/efectivo.jpg","descripcion":"$2,000 USD en efectivo como segundo premio"},{"nombre":"Set de maletas premium","posicion":3,"imagen":"assets/images/premios/maletas.jpg","descripcion":"Set completo de maletas premium de 3 piezas, marca reconocida"}]'>
                                        <i class="ri-shopping-cart-line me-1"></i> Comprar Tickets
                                    </button>
                                    <button class="btn btn-outline-primary w-100 btn-ver-premios" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modal_ver_premios"
                                            data-rifa-id="3"
                                            data-rifa-nombre="Rifa Viaje a Europa"
                                            data-rifa-premios='[{"nombre":"Viaje a Europa para 2 personas","posicion":1,"imagen":"assets/images/premios/viaje-europa.jpg","descripcion":"Viaje todo incluido a Europa para 2 personas, 7 días y 6 noches, incluye vuelos, hotel y desayunos"},{"nombre":"$2000 en efectivo","posicion":2,"imagen":"assets/images/premios/efectivo.jpg","descripcion":"$2,000 USD en efectivo como segundo premio"},{"nombre":"Set de maletas premium","posicion":3,"imagen":"assets/images/premios/maletas.jpg","descripcion":"Set completo de maletas premium de 3 piezas, marca reconocida"}]'>
                                        <i class="ri-image-line me-1"></i> Ver Premios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <a href="#" class="btn btn-primary btn-lg"><i class="ri-eye-line me-1"></i> Ver Todas las Rifas</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end container -->
        </section>
        <!-- end services -->

        <!-- Modal para Comprar Tickets -->
        <div class="modal fade" id="modal_comprar_ticket" tabindex="-1" aria-labelledby="modal_comprar_ticket_label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-success-subtle">
                        <h5 class="modal-title" id="modal_comprar_ticket_label">
                            <i class="ri-ticket-2-line me-2"></i><span id="modal_titulo_rifa">Comprar Tickets</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form_comprar_ticket">
                        <div class="modal-body">
                            <input type="hidden" id="rifa_id" name="rifa_id">

                            <!-- Información de la Rifa - Compacta -->
                            <div class="row g-2 mb-3">
                                <div class="col-lg-6">
                                    <div class="card border border-success mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="text-muted mb-0 small"><i class="ri-price-tag-3-line text-success"></i> Precio</p>
                                                    <h5 class="mb-0 text-success fw-bold">$<span id="precio_ticket">0.00</span></h5>
                                                </div>
                                                <i class="ri-money-dollar-circle-line text-success fs-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="card border border-primary mb-0">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <p class="text-muted mb-0 small"><i class="ri-ticket-line text-primary"></i> Disponibles</p>
                                                    <h5 class="mb-0 text-primary fw-bold"><span id="tickets_disponibles">0</span>/<span id="tickets_total">0</span></h5>
                                                </div>
                                                <i class="ri-coupon-line text-primary fs-24"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Premios de la Rifa - Compacta -->
                            <div class="alert alert-success border-0 mb-3 py-2">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <strong class="me-2"><i class="ri-trophy-line"></i> Premios:</strong>
                                    <div id="lista_premios" class="d-flex flex-wrap gap-2">
                                        <!-- Se llenará dinámicamente -->
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-4">

                            <h5 class="mb-3"><i class="ri-user-line"></i> Datos del Participante</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="nombre_completo" class="form-label fs-13">
                                            Nombre Completo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control bg-light border-light" id="nombre_completo" name="nombre_completo"
                                            placeholder="Ingrese su nombre completo">
                                        <div class="text-danger small mt-1" id="nombre_completo_error" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="email_participante" class="form-label fs-13">
                                            Correo Electrónico <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control bg-light border-light" id="email_participante" name="email_participante"
                                            placeholder="correo@ejemplo.com">
                                        <div class="text-danger small mt-1" id="email_participante_error" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="telefono" class="form-label fs-13">
                                            Teléfono / WhatsApp <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control bg-light border-light" id="telefono" name="telefono"
                                            placeholder="+52 1 55 1234 5678">
                                        <div class="text-danger small mt-1" id="telefono_error" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="ciudad" class="form-label fs-13">
                                            Ciudad
                                        </label>
                                        <input type="text" class="form-control bg-light border-light" id="ciudad" name="ciudad"
                                            placeholder="Ingrese su ciudad">
                                        <div class="text-danger small mt-1" id="ciudad_error" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-4">

                            <h5 class="mb-3"><i class="ri-shopping-cart-line"></i> Cantidad de Tickets</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="cantidad_tickets" class="form-label fs-13">
                                            ¿Cuántos tickets deseas comprar? <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary bg-light border-light" type="button" id="btn_menos">
                                                <i class="ri-subtract-line"></i>
                                            </button>
                                            <input type="number" class="form-control bg-light border-light text-center" id="cantidad_tickets" 
                                                   name="cantidad_tickets" value="1" min="1" max="999">
                                            <button class="btn btn-outline-secondary bg-light border-light" type="button" id="btn_mas">
                                                <i class="ri-add-line"></i>
                                            </button>
                                        </div>
                                        <div class="text-danger small mt-1" id="cantidad_tickets_error" style="display: none;"></div>
                                        <small class="text-muted">Mientras más tickets, mayores probabilidades de ganar</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label fs-13">Total a Pagar</label>
                                        <div class="card bg-primary-subtle border-0">
                                            <div class="card-body py-3">
                                                <h2 class="mb-0 text-primary">$<span id="total_pagar">0.00</span></h2>
                                                <small class="text-muted">
                                                    <span id="cantidad_display">1</span> ticket(s) × $<span id="precio_display">0.00</span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-4">

                            <h5 class="mb-3"><i class="ri-bank-card-line"></i> Método de Pago</h5>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info border-0" role="alert">
                                        <h6 class="alert-heading mb-3"><i class="ri-bank-card-line me-1"></i> Métodos de Pago Disponibles</h6>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-2">
                                                <div class="card bg-white border h-100">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="avatar-xs me-2">
                                                                <div class="avatar-title bg-primary-subtle text-primary rounded">
                                                                    <i class="ri-bank-line"></i>
                                                                </div>
                                                            </div>
                                                            <strong>Banco Interbank</strong>
                                                        </div>
                                                        <p class="mb-1 small text-muted">Cuenta: <strong class="text-dark">[Ingresar cuenta]</strong></p>
                                                        <p class="mb-0 small text-muted">CCI: <strong class="text-dark">[Ingresar CCI]</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="card bg-white border h-100">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <div class="avatar-xs me-2">
                                                                <div class="avatar-title bg-primary-subtle text-primary rounded">
                                                                    <i class="ri-bank-line"></i>
                                                                </div>
                                                            </div>
                                                            <strong>Banco BCP</strong>
                                                        </div>
                                                        <p class="mb-1 small text-muted">Cuenta: <strong class="text-dark">[Ingresar cuenta]</strong></p>
                                                        <p class="mb-0 small text-muted">CCI: <strong class="text-dark">[Ingresar CCI]</strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <div class="card bg-white border h-100">
                                                    <div class="card-body p-3 text-center">
                                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                                            <div class="avatar-xs me-2">
                                                                <div class="avatar-title bg-success-subtle text-success rounded">
                                                                    <i class="ri-smartphone-line"></i>
                                                                </div>
                                                            </div>
                                                            <strong>Yape</strong>
                                                        </div>
                                                        
                                                        <!-- QR Code Yape -->
                                                        <div class="mb-2">
                                                            <img src="assets/images/qr/yape-qr.png" alt="QR Yape" 
                                                                 class="img-fluid rounded border" 
                                                                 style="max-width: 150px; height: auto;"
                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                            <div class="alert alert-secondary border-0 p-2 mb-0" style="display: none;">
                                                                <small><i class="ri-qr-code-line"></i> QR no disponible</small>
                                                            </div>
                                                        </div>
                                                        
                                                        <p class="mb-0 small text-muted">Número: <strong class="text-dark">519 873 862</strong></p>
                                                        <small class="text-success"><i class="ri-qr-scan-line"></i> Escanea el QR o usa el número</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <div class="card bg-white border h-100">
                                                    <div class="card-body p-3 text-center">
                                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                                            <div class="avatar-xs me-2">
                                                                <div class="avatar-title bg-info-subtle text-info rounded">
                                                                    <i class="ri-smartphone-line"></i>
                                                                </div>
                                                            </div>
                                                            <strong>Plin</strong>
                                                        </div>
                                                        
                                                        <!-- QR Code Plin -->
                                                        <div class="mb-2">
                                                            <img src="assets/images/qr/plin-qr.png" alt="QR Plin" 
                                                                 class="img-fluid rounded border" 
                                                                 style="max-width: 150px; height: auto;"
                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                            <div class="alert alert-secondary border-0 p-2 mb-0" style="display: none;">
                                                                <small><i class="ri-qr-code-line"></i> QR no disponible</small>
                                                            </div>
                                                        </div>
                                                        
                                                        <p class="mb-0 small text-muted">Número: <strong class="text-dark">987 555 555</strong></p>
                                                        <small class="text-info"><i class="ri-qr-scan-line"></i> Escanea el QR o usa el número</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning border-0 mt-3 mb-0">
                                            <small>
                                                <i class="ri-information-line me-1"></i>
                                                <strong>Importante:</strong> Una vez realizado el pago, sube tu comprobante abajo o envíalo por correo/WhatsApp para validar tu compra.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="comprobante_pago" class="form-label fs-13">
                                            <i class="ri-image-add-line me-1"></i> Comprobante de Pago (Opcional)
                                        </label>
                                        <input type="file" class="form-control bg-light border-light" id="comprobante_pago" name="comprobante_pago"
                                            accept="image/*,.pdf">
                                        <div class="text-danger small mt-1" id="comprobante_pago_error" style="display: none;"></div>
                                        <small class="text-muted">Si ya realizaste el pago, puedes subir tu comprobante ahora o enviarlo después por correo/WhatsApp. Formatos: JPG, PNG, PDF (máx. 5MB)</small>
                                        
                                        <!-- Vista previa del archivo -->
                                        <div id="preview_comprobante" class="mt-3" style="display: none;">
                                            <div class="alert alert-success border-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-file-check-line fs-20 me-2"></i>
                                                    <div>
                                                        <strong>Archivo seleccionado:</strong>
                                                        <p class="mb-0 small" id="nombre_archivo"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="mb-4">

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="acepto_terminos" name="acepto_terminos">
                                        <label class="form-check-label" for="acepto_terminos">
                                            Acepto los <a href="terminos" target="_blank" class="text-primary">términos y condiciones</a> y las 
                                            <a href="terminos" target="_blank" class="text-primary">bases del sorteo</a> <span class="text-danger">*</span>
                                        </label>
                                        <div class="text-danger small mt-1" id="acepto_terminos_error" style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                <i class="ri-close-line me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" id="btn_realizar_compra">
                                <i class="ri-shopping-bag-line me-1"></i><span id="btn_compra_text">Realizar Compra</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end modal -->

        <!-- Modal para Ver Premios -->
        <div class="modal fade" id="modal_ver_premios" tabindex="-1" aria-labelledby="modal_ver_premios_label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary-subtle">
                        <h5 class="modal-title" id="modal_ver_premios_label">
                            <i class="ri-gift-line me-2"></i><span id="modal_premios_rifa_nombre">Premios de la Rifa</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="galeria_premios" class="row g-4">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal ver premios -->

        <!-- start premios -->
        <section class="section bg-light py-5" id="premios">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8">
                        <div class="text-center">
                            <h1 class="mb-3 ff-secondary fw-semibold"><i class="ri-gift-line text-success"></i> Premios Destacados</h1>
                            <p class="text-muted">Conoce los increíbles premios que puedes ganar. Todos nuestros premios son 100% reales y garantizados.</p>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center gy-4">
                    <div class="col-lg-6 col-sm-7 mx-auto">
                        <div>
                            <img src="assets/images/landing/features/img-1.png" alt="" class="img-fluid mx-auto">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-muted">
                            <div class="avatar-sm icon-effect mb-4">
                                <div class="avatar-title bg-transparent rounded-circle text-success h1">
                                    <i class="ri-gift-2-line fs-36"></i>
                                </div>
                            </div>
                            <h3 class="mb-3 fs-20">Premios de Alta Calidad</h3>
                            <p class="mb-4 ff-secondary fs-16">Todos nuestros premios son originales y nuevos. Trabajamos con las mejores marcas para garantizar tu satisfacción.</p>

                            <div class="row pt-3">
                                <div class="col-4">
                                    <div class="text-center">
                                        <h4><i class="ri-smartphone-line text-success"></i></h4>
                                        <p>Electrónica</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h4><i class="ri-car-line text-primary"></i></h4>
                                        <p>Vehículos</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <h4><i class="ri-flight-takeoff-line text-warning"></i></h4>
                                        <p>Viajes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end premios -->

        <!-- start cta -->
        <section class="py-5 bg-success position-relative">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-sm">
                        <div>
                            <h4 class="text-white mb-0 fw-semibold"><i class="ri-trophy-line me-2"></i>¡No pierdas la oportunidad de ganar increíbles premios!</h4>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-sm-auto">
                        <div>
                            <a href="#rifas" class="btn bg-gradient btn-light"><i class="ri-ticket-2-line align-middle me-1"></i> Comprar Tickets Ahora</a>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end cta -->

        <!-- start como participar -->
        <section class="section" id="como-participar">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8">
                        <div class="text-center">
                            <h1 class="mb-3 ff-secondary fw-semibold"><i class="ri-lightbulb-line text-primary"></i> ¿Cómo Participar?</h1>
                            <p class="text-muted">Participar es muy fácil y rápido. Sigue estos simples pasos y estarás dentro del sorteo.</p>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center gy-4">
                    <div class="col-lg-6 order-2 order-lg-1">
                        <div class="text-muted">
                            <h5 class="fs-12 text-uppercase text-success">Proceso Simple</h5>
                            <h4 class="mb-3">4 Pasos para Participar</h4>
                            <p class="mb-4 ff-secondary">Nuestro sistema es seguro, transparente y muy fácil de usar. Solo necesitas seguir estos pasos:</p>

                            <div class="vstack gap-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm icon-effect">
                                            <div class="avatar-title bg-soft-success text-success rounded-circle fs-18 fw-bold">
                                                1
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fs-16 mb-1">Elige tu Rifa</h5>
                                        <p class="text-muted mb-0">Navega por nuestras rifas activas y selecciona la que más te guste</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm icon-effect">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle fs-18 fw-bold">
                                                2
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fs-16 mb-1">Compra tus Tickets</h5>
                                        <p class="text-muted mb-0">Selecciona la cantidad de tickets que deseas comprar</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm icon-effect">
                                            <div class="avatar-title bg-soft-warning text-warning rounded-circle fs-18 fw-bold">
                                                3
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fs-16 mb-1">Realiza el Pago</h5>
                                        <p class="text-muted mb-0">Paga mediante transferencia, depósito o método disponible</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm icon-effect">
                                            <div class="avatar-title bg-soft-danger text-danger rounded-circle fs-18 fw-bold">
                                                4
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fs-16 mb-1">Sube tu Comprobante</h5>
                                        <p class="text-muted mb-0">Sube tu comprobante de pago y espera la validación. ¡Listo! Ya estás participando</p>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 mt-4">
                                <div class="d-flex align-items-start">
                                    <i class="ri-live-line text-danger fs-20 me-2"></i>
                                    <div>
                                        <strong>¡Ve el Sorteo EN VIVO!</strong>
                                        <p class="mb-0 small">Todos nuestros sorteos son transmitidos en vivo por TikTok, Facebook e Instagram. 
                                        Recibirás una notificación para que puedas verlo en tiempo real.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="#rifas" class="btn btn-success btn-lg"><i class="ri-ticket-2-line me-1"></i> Empezar Ahora</a>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-6 col-sm-7 col-10 ms-auto order-1 order-lg-2">
                        <div>
                            <img src="assets/images/landing/features/img-2.png" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row align-items-center mt-5 pt-lg-5 gy-4">
                    <div class="col-lg-6 col-sm-7 col-10 mx-auto">
                        <div>
                            <img src="assets/images/landing/features/img-3.png" alt="" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-muted ps-lg-5">
                            <h5 class="fs-12 text-uppercase text-success">Seguridad</h5>
                            <h4 class="mb-3">Transparencia Total</h4>
                            <p class="mb-4">Nos tomamos muy en serio la transparencia y seguridad. Todas nuestras rifas son 100% legales y verificables.</p>

                            <div class="vstack gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs icon-effect">
                                            <div class="avatar-title bg-transparent text-success rounded-circle h2">
                                                <i class="ri-shield-check-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0"><strong>Sistema Seguro:</strong> Protección de datos y transacciones encriptadas</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs icon-effect">
                                            <div class="avatar-title bg-transparent text-success rounded-circle h2">
                                                <i class="ri-eye-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0"><strong>Transparencia:</strong> Sorteos públicos en vivo por TikTok, Facebook e Instagram</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar-xs icon-effect">
                                            <div class="avatar-title bg-transparent text-success rounded-circle h2">
                                                <i class="ri-customer-service-2-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0"><strong>Soporte 24/7:</strong> Estamos aquí para ayudarte en todo momento</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end features -->

        <!-- start live streaming info -->
        <section class="py-4 bg-gradient-info position-relative">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 mx-auto text-center">
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                            <div class="avatar-sm">
                                <div class="avatar-title bg-white rounded-circle">
                                    <i class="ri-live-line text-danger fs-20"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-white mb-0"><i class="ri-video-line me-2"></i>Sorteos 100% Transparentes</h5>
                                <p class="text-white-50 mb-0 small">Transmitidos EN VIVO por nuestras redes sociales</p>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="https://www.tiktok.com/@sistemarifas" target="_blank" class="btn btn-light btn-sm">
                                    <i class="ri-tiktok-fill me-1"></i>TikTok
                                </a>
                                <a href="https://www.facebook.com/sistemarifas" target="_blank" class="btn btn-light btn-sm">
                                    <i class="ri-facebook-fill me-1"></i>Facebook
                                </a>
                                <a href="https://www.instagram.com/sistemarifas" target="_blank" class="btn btn-light btn-sm">
                                    <i class="ri-instagram-line me-1"></i>Instagram
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end live streaming info -->

        <!-- start ganadores -->
        <section class="section bg-light" id="ganadores">
            <div class="bg-overlay bg-overlay-pattern"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold"><i class="ri-trophy-line text-warning"></i> Últimos Ganadores</h1>
                            <p class="text-muted mb-4">Estos son algunos de nuestros ganadores más recientes. ¡Tú podrías ser el próximo!</p>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row gy-4">
                    <div class="col-lg-4">
                        <div class="card border shadow-sm mb-0">
                            <div class="card-body p-4 text-center">
                                <div class="avatar-xl mx-auto mb-3">
                                    <img src="assets/images/users/avatar-2.jpg" alt="" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="mb-1">María González</h5>
                                <p class="text-muted mb-3">Ciudad de México</p>
                                <div class="badge badge-soft-success mb-3 fs-13">
                                    <i class="ri-trophy-line me-1"></i> iPhone 15 Pro Max
                                </div>
                                <p class="text-muted mb-3"><i class="ri-calendar-line"></i> Ganador: 15 Oct 2025</p>
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-soft-success text-success fs-1 rounded">
                                        <i class="ri-smartphone-line"></i>
                                    </div>
                                </div>
                                <p class="text-muted small mb-0"><i class="ri-double-quotes-l"></i> No podía creer cuando me avisaron que había ganado. ¡El proceso fue super transparente! <i class="ri-double-quotes-r"></i></p>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <div class="card border shadow-sm mb-0 ribbon-box right">
                            <div class="ribbon-two ribbon-two-warning"><span>Reciente</span></div>
                            <div class="card-body p-4 text-center">
                                <div class="avatar-xl mx-auto mb-3">
                                    <img src="assets/images/users/avatar-10.jpg" alt="" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="mb-1">Carlos Ramírez</h5>
                                <p class="text-muted mb-3">Guadalajara, MX</p>
                                <div class="badge badge-soft-danger mb-3 fs-13">
                                    <i class="ri-trophy-line me-1"></i> Automóvil 2025
                                </div>
                                <p class="text-muted mb-3"><i class="ri-calendar-line"></i> Ganador: 28 Oct 2025</p>
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-soft-danger text-danger fs-1 rounded">
                                        <i class="ri-car-line"></i>
                                    </div>
                                </div>
                                <p class="text-muted small mb-0"><i class="ri-double-quotes-l"></i> ¡Increíble! Nunca pensé ganar un auto. El sistema es muy confiable y serio. <i class="ri-double-quotes-r"></i></p>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4">
                        <div class="card border shadow-sm mb-0">
                            <div class="card-body p-4 text-center">
                                <div class="avatar-xl mx-auto mb-3">
                                    <img src="assets/images/users/avatar-3.jpg" alt="" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="mb-1">Ana Martínez</h5>
                                <p class="text-muted mb-3">Monterrey, MX</p>
                                <div class="badge badge-soft-warning mb-3 fs-13">
                                    <i class="ri-trophy-line me-1"></i> Viaje a Europa
                                </div>
                                <p class="text-muted mb-3"><i class="ri-calendar-line"></i> Ganador: 5 Nov 2025</p>
                                <div class="avatar-sm mx-auto mb-2">
                                    <div class="avatar-title bg-soft-warning text-warning fs-1 rounded">
                                        <i class="ri-flight-takeoff-line"></i>
                                    </div>
                                </div>
                                <p class="text-muted small mb-0"><i class="ri-double-quotes-l"></i> Mi familia y yo viajaremos a Europa gracias a esta rifa. ¡Totalmente recomendado! <i class="ri-double-quotes-r"></i></p>
                            </div>
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
            <!-- end container -->
        </section>
        <!-- end plan -->

        <!-- start faqs -->
        <section class="section" id="faqs">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold"><i class="ri-question-answer-line text-info"></i> Preguntas Frecuentes</h1>
                            <p class="text-muted mb-4 ff-secondary">Si no encuentras respuesta a tu pregunta, puedes contactarnos. ¡Te responderemos rápidamente!</p>

                            <div class="">
                                <a href="#contact" class="btn btn-primary btn-label rounded-pill"><i class="ri-mail-line label-icon align-middle rounded-pill fs-16 me-2"></i> Contáctanos</a>
                                <a href="https://wa.me/" target="_blank" class="btn btn-success btn-label rounded-pill"><i class="ri-whatsapp-line label-icon align-middle rounded-pill fs-16 me-2"></i> WhatsApp</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row g-lg-5 g-4">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0 me-1">
                                <i class="ri-question-line fs-24 align-middle text-success me-1"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0 fw-semibold">Preguntas Generales</h5>
                            </div>
                        </div>
                        <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box" id="genques-accordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="genques-headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#genques-collapseOne" aria-expanded="true" aria-controls="genques-collapseOne">
                                        ¿Cómo puedo participar en una rifa?
                                    </button>
                                </h2>
                                <div id="genques-collapseOne" class="accordion-collapse collapse show" aria-labelledby="genques-headingOne" data-bs-parent="#genques-accordion">
                                    <div class="accordion-body ff-secondary">
                                        Es muy sencillo: navega por nuestras rifas activas, selecciona la que más te guste, elige la cantidad de tickets que deseas comprar, 
                                        realiza el pago mediante alguno de nuestros métodos disponibles y sube tu comprobante de pago. Una vez validado, ya estarás participando.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="genques-headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#genques-collapseTwo" aria-expanded="false" aria-controls="genques-collapseTwo">
                                        ¿Cuántos tickets puedo comprar?
                                    </button>
                                </h2>
                                <div id="genques-collapseTwo" class="accordion-collapse collapse" aria-labelledby="genques-headingTwo" data-bs-parent="#genques-accordion">
                                    <div class="accordion-body ff-secondary">
                                        Puedes comprar la cantidad de tickets que desees, siempre y cuando haya disponibilidad. Mientras más tickets tengas, 
                                        mayores serán tus probabilidades de ganar. No hay límite máximo de compra.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="genques-headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#genques-collapseThree" aria-expanded="false" aria-controls="genques-collapseThree">
                                        ¿Cuánto tiempo tarda la validación del pago?
                                    </button>
                                </h2>
                                <div id="genques-collapseThree" class="accordion-collapse collapse" aria-labelledby="genques-headingThree" data-bs-parent="#genques-accordion">
                                    <div class="accordion-body ff-secondary">
                                        Nuestro equipo valida los comprobantes de pago en un plazo máximo de 24 horas hábiles. Una vez validado tu pago, 
                                        recibirás una confirmación por correo electrónico con tus números de ticket asignados.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="genques-headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#genques-collapseFour" aria-expanded="false" aria-controls="genques-collapseFour">
                                        ¿Puedo ver mis tickets comprados?
                                    </button>
                                </h2>
                                <div id="genques-collapseFour" class="accordion-collapse collapse" aria-labelledby="genques-headingFour" data-bs-parent="#genques-accordion">
                                    <div class="accordion-body ff-secondary">
                                        Sí, una vez que tu pago sea validado, recibirás un correo con tus números de ticket. También puedes consultar 
                                        el estado de tu compra en cualquier momento contactándonos con tu código de ticket.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end accordion-->

                    </div>
                    <!-- end col -->
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0 me-1">
                                <i class="ri-shield-keyhole-line fs-24 align-middle text-success me-1"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0 fw-semibold">Sorteos y Premios</h5>
                            </div>
                        </div>

                        <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box" id="privacy-accordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="privacy-headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#privacy-collapseOne" aria-expanded="false" aria-controls="privacy-collapseOne">
                                        ¿Cómo se realiza el sorteo?
                                    </button>
                                </h2>
                                <div id="privacy-collapseOne" class="accordion-collapse collapse" aria-labelledby="privacy-headingOne" data-bs-parent="#privacy-accordion">
                                    <div class="accordion-body ff-secondary">
                                        El sorteo se realiza de forma transparente y pública en la fecha indicada. <strong>Transmitimos en vivo por TikTok, Facebook e Instagram</strong> 
                                        para que todos puedan ver el proceso. Utilizamos un sistema de sorteo aleatorio certificado que garantiza la imparcialidad. 
                                        Todos los sorteos son grabados y publicados en nuestras redes sociales para total transparencia.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="privacy-headingTwo">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#privacy-collapseTwo" aria-expanded="true" aria-controls="privacy-collapseTwo">
                                        ¿Los premios son reales?
                                    </button>
                                </h2>
                                <div id="privacy-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="privacy-headingTwo" data-bs-parent="#privacy-accordion">
                                    <div class="accordion-body ff-secondary">
                                        ¡Absolutamente! Todos nuestros premios son 100% reales y originales. Trabajamos con las mejores marcas y proveedores
                                        para garantizar que recibas productos de la más alta calidad. Puedes verificar los ganadores anteriores en nuestra galería.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="privacy-headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#privacy-collapseThree" aria-expanded="false" aria-controls="privacy-collapseThree">
                                        ¿Cómo me entregan el premio si gano?
                                    </button>
                                </h2>
                                <div id="privacy-collapseThree" class="accordion-collapse collapse" aria-labelledby="privacy-headingThree" data-bs-parent="#privacy-accordion">
                                    <div class="accordion-body ff-secondary">
                                        Una vez que el sorteo se realice, nos pondremos en contacto contigo inmediatamente mediante correo y teléfono.
                                        Coordinaremos la entrega del premio en tu ubicación o punto de encuentro acordado. La entrega incluye documentación oficial.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="privacy-headingFour">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#privacy-collapseFour" aria-expanded="false" aria-controls="privacy-collapseFour">
                                        ¿Puedo verificar la transparencia del sorteo?
                                    </button>
                                </h2>
                                <div id="privacy-collapseFour" class="accordion-collapse collapse" aria-labelledby="privacy-headingFour" data-bs-parent="#privacy-accordion">
                                    <div class="accordion-body ff-secondary">
                                        ¡Absolutamente! Nos tomamos muy en serio la transparencia. <strong>Transmitimos todos los sorteos EN VIVO por TikTok (@sistemarifas), 
                                        Facebook Live e Instagram Live.</strong> Cualquier persona puede ver el sorteo en tiempo real. Además, grabamos y archivamos todas las transmisiones, 
                                        y publicamos los resultados con hash de verificación para que cualquier participante pueda comprobar la legitimidad del sorteo.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end accordion-->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end faqs -->

        <!-- start contact -->
        <section class="section" id="contact">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold"><i class="ri-mail-line text-primary"></i> Contáctanos</h1>
                            <p class="text-muted mb-4 ff-secondary">¿Tienes alguna pregunta? Estamos aquí para ayudarte. Contactanos y te responderemos lo más pronto posible.</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row gy-4">
                    <div class="col-lg-4">
                        <div>
                            <div class="mt-4">
                                <h5 class="fs-13 text-muted text-uppercase">Correo Electrónico:</h5>
                                <div class="ff-secondary fw-semibold">info@sistemrifas.com</div>
                            </div>
                            <div class="mt-4">
                                <h5 class="fs-13 text-muted text-uppercase">WhatsApp:</h5>
                                <div class="ff-secondary fw-semibold">+52 1 55 1234 5678</div>
                            </div>
                            <div class="mt-4">
                                <h5 class="fs-13 text-muted text-uppercase">Horario de Atención:</h5>
                                <div class="ff-secondary fw-semibold">Lunes a Domingo 9:00am a 9:00pm</div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-8">
                        <div>
                            <form>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="name" class="form-label fs-13">Nombre</label>
                                            <input name="name" id="name" type="text" class="form-control bg-light border-light" placeholder="Tu nombre*">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="email" class="form-label fs-13">Correo</label>
                                            <input name="email" id="email" type="email" class="form-control bg-light border-light" placeholder="Tu correo*">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-4">
                                            <label for="subject" class="form-label fs-13">Asunto</label>
                                            <input type="text" class="form-control bg-light border-light" id="subject" name="subject" placeholder="Asunto" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="comments" class="form-label fs-13">Mensaje</label>
                                            <textarea name="comments" id="comments" rows="5" class="form-control bg-light border-light" placeholder="Tu mensaje..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <button type="submit" class="btn btn-primary">Enviar Mensaje <i class="ri-send-plane-fill align-middle ms-1"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end contact -->

        <!-- start consultar tickets -->
        <section class="section bg-light" id="consultar-tickets">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center mb-5">
                            <h1 class="mb-3 ff-secondary fw-semibold"><i class="ri-search-line text-info"></i> Consultar mis Tickets</h1>
                            <p class="text-muted mb-4 ff-secondary">Ingresa tu número de documento o código de ticket para ver el estado de tus compras y los números asignados.</p>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border shadow-none">
                            <div class="card-body p-4">
                                <form id="form_consultar_tickets">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="documento_ticket" class="form-label fs-13">
                                                    <i class="ri-file-text-line me-1"></i> Número de Documento o Código de Ticket
                                                </label>
                                                <input type="text" class="form-control bg-light border-light" id="documento_ticket" name="documento_ticket"
                                                    placeholder="Ej: 12345678 o TICKET-2025-001">
                                                <div class="text-danger small mt-1" id="documento_ticket_error" style="display: none;"></div>
                                                <small class="text-muted">Puedes buscar por tu DNI, pasaporte o código de ticket</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-4">
                                                <label for="email_consulta" class="form-label fs-13">
                                                    <i class="ri-mail-line me-1"></i> Correo Electrónico
                                                </label>
                                                <input type="email" class="form-control bg-light border-light" id="email_consulta" name="email_consulta"
                                                    placeholder="correo@ejemplo.com">
                                                <div class="text-danger small mt-1" id="email_consulta_error" style="display: none;"></div>
                                                <small class="text-muted">El correo con el que realizaste la compra</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="ri-search-line me-1"></i> Consultar Tickets
                                        </button>
                                    </div>
                                </form>

                                <!-- Resultados de la consulta -->
                                <div id="resultados_tickets" class="mt-4" style="display: none;">
                                    <hr>
                                    <h5 class="mb-3"><i class="ri-ticket-line text-success"></i> Tus Tickets</h5>
                                    <div id="lista_tickets_usuario">
                                        <!-- Se llenará dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end consultar tickets -->

        <!-- start cta -->
        <section class="py-5 bg-primary position-relative">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-sm">
                        <div>
                            <h4 class="text-white mb-0 fw-semibold"><i class="ri-trophy-line me-2"></i> ¡Participa ahora y gana increíbles premios!</h4>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-sm-auto">
                        <div>
                            <a href="#rifas" class="btn bg-gradient btn-light"><i class="ri-ticket-2-line align-middle me-1"></i> Ver Rifas Activas</a>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </section>
        <!-- end cta -->

        <!-- Start footer -->
        <footer class="custom-footer bg-dark py-5 position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mt-4">
                        <div>
                            <div>
                                <img src="assets/images/logo-light.png" alt="logo light" height="17">
                            </div>
                            <div class="mt-4 fs-13">
                                <p>Sistema Profesional de Rifas</p>
                                <p class="ff-secondary">Plataforma confiable y transparente para rifas y sorteos. Participa por increíbles premios de forma segura.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 ms-lg-auto">
                        <div class="row">
                            <div class="col-sm-4 mt-4">
                                <h5 class="text-white mb-0">Enlaces Rápidos</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="#rifas">Rifas Activas</a></li>
                                        <li><a href="#premios">Premios</a></li>
                                        <li><a href="#ganadores">Ganadores</a></li>
                                        <li><a href="#como-participar">Cómo Participar</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-4">
                                <h5 class="text-white mb-0">Información</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="#consultar-tickets">Consultar mis Tickets</a></li>
                                        <li><a href="#faqs">Preguntas Frecuentes</a></li>
                                        <li><a href="#contact">Contacto</a></li>
                                        <li><a href="terminos" target="_blank">Términos y Condiciones</a></li>
                                        <li><a href="terminos" target="_blank">Privacidad</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-4 mt-4">
                                <h5 class="text-white mb-0">Administración</h5>
                                <div class="text-muted mt-3">
                                    <ul class="list-unstyled ff-secondary footer-list">
                                        <li><a href="admin-login">Iniciar Sesión</a></li>
                                        <li><a href="admin-dashboard">Panel Admin</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row text-center text-sm-start align-items-center mt-5">
                    <div class="col-sm-6">
                        <div>
                            <p class="copy-rights mb-0">
                                <script> document.write(new Date().getFullYear()) </script> © Sistema de Rifas - Todos los derechos reservados
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end mt-3 mt-sm-0">
                            <ul class="list-inline mb-0 footer-social-link">
                                <li class="list-inline-item">
                                    <a href="https://www.tiktok.com/@sistemarifas" target="_blank" class="avatar-xs d-block" title="Síguenos en TikTok - Sorteos EN VIVO">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-tiktok-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://www.facebook.com/sistemarifas" target="_blank" class="avatar-xs d-block" title="Síguenos en Facebook">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-facebook-fill"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://www.instagram.com/sistemarifas" target="_blank" class="avatar-xs d-block" title="Síguenos en Instagram">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-instagram-line"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://wa.me/5215512345678" target="_blank" class="avatar-xs d-block" title="Contáctanos por WhatsApp">
                                        <div class="avatar-title rounded-circle">
                                            <i class="ri-whatsapp-line"></i>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end footer -->

    </div>
    <!-- end layout wrapper -->


    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!--Swiper slider js-->
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- landing init -->
    <script src="assets/js/pages/landing.init.js"></script>

    <!-- Script para Contadores Regresivos -->
    <script>
        // Función para actualizar contador regresivo
        function actualizarContador(elemento, fechaObjetivo) {
            const ahora = new Date().getTime();
            const distancia = fechaObjetivo - ahora;

            if (distancia < 0) {
                // Si ya pasó la fecha
                const days = elemento.querySelector('.countdown-days');
                const hours = elemento.querySelector('.countdown-hours');
                const minutes = elemento.querySelector('.countdown-minutes');
                const seconds = elemento.querySelector('.countdown-seconds');
                
                if (days) days.textContent = '00';
                if (hours) hours.textContent = '00';
                if (minutes) minutes.textContent = '00';
                if (seconds) seconds.textContent = '00';
                return;
            }

            const dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((distancia % (1000 * 60)) / 1000);

            const days = elemento.querySelector('.countdown-days');
            const hours = elemento.querySelector('.countdown-hours');
            const minutes = elemento.querySelector('.countdown-minutes');
            const seconds = elemento.querySelector('.countdown-seconds');
            
            if (days) days.textContent = dias.toString().padStart(2, '0');
            if (hours) hours.textContent = horas.toString().padStart(2, '0');
            if (minutes) minutes.textContent = minutos.toString().padStart(2, '0');
            if (seconds) seconds.textContent = segundos.toString().padStart(2, '0');
        }

        // Inicializar todos los contadores
        document.addEventListener('DOMContentLoaded', function() {
            // Contador del Hero
            const heroCountdown = {
                days: document.getElementById('hero-days'),
                hours: document.getElementById('hero-hours'),
                minutes: document.getElementById('hero-minutes'),
                seconds: document.getElementById('hero-seconds'),
                fecha: new Date('2025-12-31T20:00:00').getTime()
            };

            // Actualizar contador del hero
            function actualizarHeroCountdown() {
                const ahora = new Date().getTime();
                const distancia = heroCountdown.fecha - ahora;

                if (distancia < 0) {
                    heroCountdown.days.textContent = '00';
                    heroCountdown.hours.textContent = '00';
                    heroCountdown.minutes.textContent = '00';
                    heroCountdown.seconds.textContent = '00';
                    return;
                }

                const dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
                const horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
                const segundos = Math.floor((distancia % (1000 * 60)) / 1000);

                heroCountdown.days.textContent = dias.toString().padStart(2, '0');
                heroCountdown.hours.textContent = horas.toString().padStart(2, '0');
                heroCountdown.minutes.textContent = minutos.toString().padStart(2, '0');
                heroCountdown.seconds.textContent = segundos.toString().padStart(2, '0');
            }

            // Actualizar contadores de rifas
            const contadoresRifas = document.querySelectorAll('.countdown-rifa');
            contadoresRifas.forEach(contador => {
                const fechaStr = contador.getAttribute('data-fecha');
                const fechaObjetivo = new Date(fechaStr).getTime();
                
                // Actualizar inmediatamente
                actualizarContador(contador, fechaObjetivo);
                
                // Actualizar cada segundo
                setInterval(() => {
                    actualizarContador(contador, fechaObjetivo);
                }, 1000);
            });

            // Actualizar hero countdown inmediatamente y cada segundo
            actualizarHeroCountdown();
            setInterval(actualizarHeroCountdown, 1000);
        });
    </script>

    <!-- Script para Modal de Compra de Tickets -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let precioUnitario = 0;
            let ticketsDisponibles = 0;

            // Evento al abrir el modal
            const modalElement = document.getElementById('modal_comprar_ticket');
            modalElement.addEventListener('show.bs.modal', function (event) {
                // Botón que disparó el modal
                const button = event.relatedTarget;
                
                // Extraer información de los data attributes
                const rifaId = button.getAttribute('data-rifa-id');
                const rifaNombre = button.getAttribute('data-rifa-nombre');
                const rifaPrecio = button.getAttribute('data-rifa-precio');
                const rifaDisponibles = button.getAttribute('data-rifa-disponibles');
                const rifaTotal = button.getAttribute('data-rifa-total');
                const rifaPremios = JSON.parse(button.getAttribute('data-rifa-premios'));

                // Actualizar el modal
                document.getElementById('modal_titulo_rifa').textContent = rifaNombre;
                document.getElementById('rifa_id').value = rifaId;
                document.getElementById('precio_ticket').textContent = rifaPrecio;
                document.getElementById('tickets_disponibles').textContent = rifaDisponibles;
                document.getElementById('tickets_total').textContent = rifaTotal;
                document.getElementById('precio_display').textContent = rifaPrecio;

                // Guardar valores para cálculos
                precioUnitario = parseFloat(rifaPrecio);
                ticketsDisponibles = parseInt(rifaDisponibles);

                // Actualizar límite máximo de tickets
                document.getElementById('cantidad_tickets').setAttribute('max', rifaDisponibles);

                // Mostrar lista de premios
                const listaPremios = document.getElementById('lista_premios');
                listaPremios.innerHTML = '';
                
                rifaPremios.forEach((premio, index) => {
                    const badgeClass = index === 0 ? 'bg-warning text-white' : index === 1 ? 'bg-secondary text-white' : 'bg-dark text-white';
                    listaPremios.innerHTML += `
                        <span class="badge ${badgeClass} fs-13 px-2 py-2">
                            ${premio.posicion}° ${premio.nombre}
                        </span>
                    `;
                });

                // Resetear cantidad a 1
                document.getElementById('cantidad_tickets').value = 1;
                calcularTotal();
            });

            // Función para calcular el total
            function calcularTotal() {
                const cantidad = parseInt(document.getElementById('cantidad_tickets').value) || 1;
                const total = (cantidad * precioUnitario).toFixed(2);
                
                document.getElementById('total_pagar').textContent = total;
                document.getElementById('cantidad_display').textContent = cantidad;
            }

            // Botón aumentar cantidad
            document.getElementById('btn_mas').addEventListener('click', function() {
                const input = document.getElementById('cantidad_tickets');
                let valor = parseInt(input.value) || 1;
                const max = parseInt(input.getAttribute('max'));
                
                if (valor < max) {
                    input.value = valor + 1;
                    calcularTotal();
                }
            });

            // Botón disminuir cantidad
            document.getElementById('btn_menos').addEventListener('click', function() {
                const input = document.getElementById('cantidad_tickets');
                let valor = parseInt(input.value) || 1;
                
                if (valor > 1) {
                    input.value = valor - 1;
                    calcularTotal();
                }
            });

            // Evento al cambiar la cantidad manualmente
            document.getElementById('cantidad_tickets').addEventListener('input', function() {
                let valor = parseInt(this.value) || 1;
                const max = parseInt(this.getAttribute('max'));
                
                if (valor < 1) {
                    this.value = 1;
                } else if (valor > max) {
                    this.value = max;
                    alert(`Solo hay ${max} tickets disponibles`);
                }
                
                calcularTotal();
            });

            // Función para limpiar errores
            function limpiarErrores() {
                // Remover clases de error de todos los inputs
                document.querySelectorAll('#form_comprar_ticket .form-control').forEach(input => {
                    input.classList.remove('border-danger');
                });
                
                // Ocultar todos los mensajes de error
                document.querySelectorAll('#form_comprar_ticket [id$="_error"]').forEach(error => {
                    error.style.display = 'none';
                    error.textContent = '';
                });
                
                // Limpiar vista previa del comprobante
                document.getElementById('preview_comprobante').style.display = 'none';
            }

            // Función para mostrar error en un campo
            function mostrarError(campo, mensaje) {
                const input = document.getElementById(campo);
                const errorDiv = document.getElementById(campo + '_error');
                
                if (input) {
                    input.classList.add('border-danger');
                }
                
                if (errorDiv) {
                    errorDiv.textContent = mensaje;
                    errorDiv.style.display = 'block';
                }
            }

            // Función de validación
            function validarFormulario() {
                limpiarErrores();
                let esValido = true;

                // Validar nombre completo
                const nombreCompleto = document.getElementById('nombre_completo').value.trim();
                if (nombreCompleto === '') {
                    mostrarError('nombre_completo', 'Por favor, ingrese su nombre completo');
                    esValido = false;
                }

                // Validar email
                const email = document.getElementById('email_participante').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email === '') {
                    mostrarError('email_participante', 'Por favor, ingrese su correo electrónico');
                    esValido = false;
                } else if (!emailRegex.test(email)) {
                    mostrarError('email_participante', 'Por favor, ingrese un correo electrónico válido');
                    esValido = false;
                }

                // Validar teléfono
                const telefono = document.getElementById('telefono').value.trim();
                if (telefono === '') {
                    mostrarError('telefono', 'Por favor, ingrese su número de teléfono');
                    esValido = false;
                }

                // Validar cantidad de tickets
                const cantidadTickets = parseInt(document.getElementById('cantidad_tickets').value);
                if (isNaN(cantidadTickets) || cantidadTickets < 1) {
                    mostrarError('cantidad_tickets', 'La cantidad debe ser al menos 1');
                    esValido = false;
                } else if (cantidadTickets > ticketsDisponibles) {
                    mostrarError('cantidad_tickets', `Solo hay ${ticketsDisponibles} tickets disponibles`);
                    esValido = false;
                }

                // Validar términos y condiciones
                const aceptoTerminos = document.getElementById('acepto_terminos').checked;
                if (!aceptoTerminos) {
                    mostrarError('acepto_terminos', 'Debe aceptar los términos y condiciones');
                    esValido = false;
                }

                return esValido;
            }

            // Limpiar error cuando el usuario empieza a escribir
            document.querySelectorAll('#form_comprar_ticket input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('border-danger');
                    const errorDiv = document.getElementById(this.id + '_error');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                        errorDiv.textContent = '';
                    }
                });
            });

            // Limpiar error del checkbox cuando cambia
            document.getElementById('acepto_terminos').addEventListener('change', function() {
                const errorDiv = document.getElementById('acepto_terminos_error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                    errorDiv.textContent = '';
                }
            });

            // Mostrar vista previa del archivo de comprobante
            document.getElementById('comprobante_pago').addEventListener('change', function() {
                const archivo = this.files[0];
                const preview = document.getElementById('preview_comprobante');
                const nombreArchivo = document.getElementById('nombre_archivo');
                
                if (archivo) {
                    nombreArchivo.textContent = archivo.name + ' (' + (archivo.size / 1024).toFixed(2) + ' KB)';
                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            });

            // Manejo del envío del formulario
            document.getElementById('form_comprar_ticket').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validar formulario
                if (!validarFormulario()) {
                    return;
                }

                // Aquí se enviaría la información al backend
                const formData = new FormData();
                formData.append('rifa_id', document.getElementById('rifa_id').value);
                formData.append('nombre_completo', document.getElementById('nombre_completo').value);
                formData.append('email', document.getElementById('email_participante').value);
                formData.append('telefono', document.getElementById('telefono').value);
                formData.append('ciudad', document.getElementById('ciudad').value);
                formData.append('cantidad_tickets', document.getElementById('cantidad_tickets').value);
                formData.append('total', document.getElementById('total_pagar').textContent);
                
                // Agregar archivo si existe
                const comprobanteInput = document.getElementById('comprobante_pago');
                if (comprobanteInput.files.length > 0) {
                    const archivo = comprobanteInput.files[0];
                    
                    // Validar tamaño (5MB)
                    if (archivo.size > 5 * 1024 * 1024) {
                        mostrarError('comprobante_pago', 'El archivo no debe superar los 5MB');
                        return;
                    }
                    
                    formData.append('comprobante_pago', archivo);
                }

                console.log('Datos de compra:', {
                    rifa_id: document.getElementById('rifa_id').value,
                    nombre_completo: document.getElementById('nombre_completo').value,
                    email: document.getElementById('email_participante').value,
                    telefono: document.getElementById('telefono').value,
                    ciudad: document.getElementById('ciudad').value,
                    cantidad_tickets: document.getElementById('cantidad_tickets').value,
                    total: document.getElementById('total_pagar').textContent,
                    tiene_comprobante: comprobanteInput.files.length > 0
                });

                // Simulación de envío exitoso
                const mensajeComprobante = comprobanteInput.files.length > 0 ? 
                    '\n\n✅ Tu comprobante será validado en las próximas 24 horas.' : 
                    '\n\nRecuerda enviar tu comprobante de pago para completar la validación.';
                
                alert('¡Compra registrada exitosamente!\n\nRecibirás un correo con las instrucciones de pago.\n\nCódigo de referencia: RIFA-' + Date.now() + mensajeComprobante);
                
                // Cerrar modal
                bootstrap.Modal.getInstance(modalElement).hide();
                
                // Resetear formulario
                this.reset();
                limpiarErrores();
            });

            // Resetear validación al cerrar el modal
            modalElement.addEventListener('hidden.bs.modal', function () {
                const form = document.getElementById('form_comprar_ticket');
                form.reset();
                limpiarErrores();
            });
        });
    </script>

    <!-- Script para Modal de Ver Premios -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalVerPremios = document.getElementById('modal_ver_premios');
            
            modalVerPremios.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const rifaNombre = button.getAttribute('data-rifa-nombre');
                const rifaPremios = JSON.parse(button.getAttribute('data-rifa-premios'));

                // Actualizar título del modal
                document.getElementById('modal_premios_rifa_nombre').textContent = rifaNombre;

                // Renderizar galería de premios
                const galeria = document.getElementById('galeria_premios');
                galeria.innerHTML = '';

                rifaPremios.forEach((premio, index) => {
                    const badgeClass = index === 0 ? 'bg-warning text-white' : index === 1 ? 'bg-secondary text-white' : 'bg-dark text-white';
                    const colClass = rifaPremios.length === 1 ? 'col-12' : rifaPremios.length === 2 ? 'col-md-6' : 'col-md-4';
                    
                    galeria.innerHTML += `
                        <div class="${colClass}">
                            <div class="card border shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <span class="badge ${badgeClass} fs-6 px-3 py-2">${premio.posicion}° Premio</span>
                                    </div>
                                    <h5 class="text-center mb-3">${premio.nombre}</h5>
                                    <div class="text-center mb-3">
                                        <img src="${premio.imagen || 'assets/images/premios/default.jpg'}" 
                                             alt="${premio.nombre}" 
                                             class="img-fluid rounded shadow-sm" 
                                             style="max-height: 300px; width: auto; object-fit: cover;"
                                             onerror="this.src='assets/images/premios/default.jpg'">
                                    </div>
                                    ${premio.descripcion ? `
                                        <p class="text-muted text-center mb-0">
                                            <small>${premio.descripcion}</small>
                                        </p>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
            });
        });
    </script>

    <!-- Script para Consultar Tickets -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formConsultar = document.getElementById('form_consultar_tickets');
            const resultadosDiv = document.getElementById('resultados_tickets');
            const listaTickets = document.getElementById('lista_tickets_usuario');

            formConsultar.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const documento = document.getElementById('documento_ticket').value.trim();
                const email = document.getElementById('email_consulta').value.trim();

                // Limpiar errores
                document.getElementById('documento_ticket').classList.remove('border-danger');
                document.getElementById('email_consulta').classList.remove('border-danger');
                document.getElementById('documento_ticket_error').style.display = 'none';
                document.getElementById('email_consulta_error').style.display = 'none';

                let hayErrores = false;

                // Validación
                if (documento === '') {
                    mostrarErrorConsulta('documento_ticket', 'Por favor, ingrese su documento o código de ticket');
                    hayErrores = true;
                }

                if (email === '') {
                    mostrarErrorConsulta('email_consulta', 'Por favor, ingrese su correo electrónico');
                    hayErrores = true;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    mostrarErrorConsulta('email_consulta', 'Por favor, ingrese un correo válido');
                    hayErrores = true;
                }

                if (hayErrores) return;

                // Simulación de consulta (aquí harías la llamada AJAX)
                console.log('Consultando tickets para:', { documento, email });

                // Ejemplo de respuesta simulada
                const ticketsSimulados = [
                    {
                        rifa: 'Rifa iPhone 15 Pro Max',
                        codigo: 'TICKET-2025-001',
                        numeros: ['001', '023', '045', '067', '089'],
                        estado: 'VALIDADO',
                        fecha_compra: '2025-11-20',
                        premio: 'iPhone 15 Pro Max 256GB'
                    }
                ];

                mostrarTickets(ticketsSimulados);
            });

            function mostrarErrorConsulta(campo, mensaje) {
                const input = document.getElementById(campo);
                const errorDiv = document.getElementById(campo + '_error');
                input.classList.add('border-danger');
                errorDiv.textContent = mensaje;
                errorDiv.style.display = 'block';
            }

            function mostrarTickets(tickets) {
                if (tickets.length === 0) {
                    listaTickets.innerHTML = `
                        <div class="alert alert-warning border-0">
                            <i class="ri-information-line me-2"></i>
                            No se encontraron tickets con los datos proporcionados. Verifica tu información.
                        </div>
                    `;
                    resultadosDiv.style.display = 'block';
                    return;
                }

                listaTickets.innerHTML = '';

                tickets.forEach(ticket => {
                    const estadoClass = ticket.estado === 'VALIDADO' ? 'bg-success' : 
                                      ticket.estado === 'PENDIENTE' ? 'bg-warning' : 'bg-secondary';
                    
                    listaTickets.innerHTML += `
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="mb-1"><i class="ri-ticket-line text-primary"></i> ${ticket.rifa}</h6>
                                        <p class="text-muted mb-0 small">Código: <strong>${ticket.codigo}</strong></p>
                                        <p class="text-muted mb-0 small">Compra: ${ticket.fecha_compra}</p>
                                    </div>
                                    <span class="badge ${estadoClass} text-white">${ticket.estado}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <strong class="d-block mb-2"><i class="ri-trophy-line text-warning"></i> Premio Participando:</strong>
                                    <p class="mb-0">${ticket.premio}</p>
                                </div>
                                
                                <div>
                                    <strong class="d-block mb-2"><i class="ri-hashtag text-info"></i> Tus Números de Ticket:</strong>
                                    <div class="d-flex flex-wrap gap-2">
                                        ${ticket.numeros.map(num => `
                                            <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2">${num}</span>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                resultadosDiv.style.display = 'block';
                
                // Scroll suave a los resultados
                resultadosDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            // Limpiar errores al escribir
            document.querySelectorAll('#form_consultar_tickets input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('border-danger');
                    const errorDiv = document.getElementById(this.id + '_error');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>

