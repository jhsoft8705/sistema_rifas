
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

        <!-- Modal para Comprar Tickets - Estilo Checkout -->
        <div class="modal fade" id="modal_comprar_ticket" tabindex="-1" aria-labelledby="modal_comprar_ticket_label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title" id="modal_comprar_ticket_label">
                            <i class="ri-shopping-cart-line me-2 text-success"></i><span id="modal_titulo_rifa">Checkout - Comprar Tickets</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form id="form_comprar_ticket">
                        <div class="modal-body p-4">
                            <input type="hidden" id="rifa_id" name="rifa_id">
                            
                            <div class="row">
                                <!-- Columna Izquierda - Formulario con Tabs -->
                                <div class="col-xl-8">
                                    <div class="card">
                                        <div class="card-body checkout-tab">
                                            
                                            <!-- Navigation Tabs -->
                                            <div class="step-arrow-nav mt-n3 mx-n3 mb-3">
                                                <ul class="nav nav-pills nav-justified custom-nav" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3 active" id="pills-personal-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-personal" 
                                                                type="button" role="tab" aria-controls="pills-personal" 
                                                                aria-selected="true" data-position="0">
                                                            <i class="ri-user-2-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2 d-none d-sm-inline-flex"></i> 
                                                            <span class="d-none d-sm-inline">Información</span>
                                                            <span class="d-inline d-sm-none"><i class="ri-user-2-line me-1"></i>Info</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3" id="pills-order-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-order" 
                                                                type="button" role="tab" aria-controls="pills-order" 
                                                                aria-selected="false" data-position="1" disabled>
                                                            <i class="ri-shopping-cart-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2 d-none d-sm-inline-flex"></i> 
                                                            <span class="d-none d-sm-inline">Tu Orden</span>
                                                            <span class="d-inline d-sm-none"><i class="ri-shopping-cart-line me-1"></i>Orden</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3" id="pills-payment-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-payment" 
                                                                type="button" role="tab" aria-controls="pills-payment" 
                                                                aria-selected="false" data-position="2" disabled>
                                                            <i class="ri-bank-card-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2 d-none d-sm-inline-flex"></i> 
                                                            <span class="d-none d-sm-inline">Pago</span>
                                                            <span class="d-inline d-sm-none"><i class="ri-bank-card-line me-1"></i>Pago</span>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link fs-15 p-3" id="pills-finish-tab" 
                                                                data-bs-toggle="pill" data-bs-target="#pills-finish" 
                                                                type="button" role="tab" aria-controls="pills-finish" 
                                                                aria-selected="false" data-position="3" disabled>
                                                            <i class="ri-checkbox-circle-line fs-16 p-2 bg-soft-primary text-primary rounded-circle align-middle me-2 d-none d-sm-inline-flex"></i> 
                                                            <span class="d-none d-sm-inline">Confirmar</span>
                                                            <span class="d-inline d-sm-none"><i class="ri-checkbox-circle-line me-1"></i>OK</span>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <!-- Tab Content -->
                                            <div class="tab-content">
                                                
                                                <!-- Tab 1: Información Personal -->
                                                <div class="tab-pane fade show active" id="pills-personal" role="tabpanel">
                                                    <div>
                                                        <h5 class="mb-1">Información Personal</h5>
                                                        <p class="text-muted mb-4">Por favor, ingresa tus datos para participar</p>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="nombre_completo" class="form-label">
                                                            Nombre Completo <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="nombre_completo" 
                                                               name="nombre_completo" placeholder="Ingrese su nombre completo">
                                                        <div class="text-danger small mt-1" id="nombre_completo_error" style="display: none;"></div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="email_participante" class="form-label">
                                                                    Correo Electrónico <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="email" class="form-control" id="email_participante" 
                                                                       name="email_participante" placeholder="correo@ejemplo.com">
                                                                <div class="text-danger small mt-1" id="email_participante_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="telefono" class="form-label">
                                                                    Teléfono / WhatsApp <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="tel" class="form-control" id="telefono" 
                                                                       name="telefono" placeholder="+52 1 55 1234 5678">
                                                                <div class="text-danger small mt-1" id="telefono_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="tipo_documento" class="form-label">
                                                                    Tipo de Documento <span class="text-danger">*</span>
                                                                </label>
                                                                <select class="form-select" id="tipo_documento" name="tipo_documento">
                                                                    <option value="">Seleccionar...</option>
                                                                    <option value="DNI">DNI</option>
                                                                    <option value="CE">Carnet de Extranjería</option>
                                                                    <option value="PASAPORTE">Pasaporte</option>
                                                                    <option value="RUC">RUC</option>
                                                                </select>
                                                                <div class="text-danger small mt-1" id="tipo_documento_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="numero_documento" class="form-label">
                                                                    Número de Documento <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="numero_documento" 
                                                                       name="numero_documento" placeholder="Ej: 12345678">
                                                                <div class="text-danger small mt-1" id="numero_documento_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="ciudad" class="form-label">
                                                                    Ciudad <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="ciudad" 
                                                                       name="ciudad" placeholder="Ej: Ciudad de México, Guadalajara...">
                                                                <div class="text-danger small mt-1" id="ciudad_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="mb-3">
                                                                <label for="estado" class="form-label">
                                                                    Estado/Provincia <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" class="form-control" id="estado" 
                                                                       name="estado" placeholder="Ej: CDMX, Jalisco...">
                                                                <div class="text-danger small mt-1" id="estado_error" style="display: none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label for="direccion_envio" class="form-label">
                                                            Dirección de Envío Completa <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control" id="direccion_envio" name="direccion_envio" 
                                                                  rows="3" placeholder="Calle, número, colonia, código postal, referencias..."></textarea>
                                                        <div class="text-danger small mt-1" id="direccion_envio_error" style="display: none;"></div>
                                                        <small class="text-muted">
                                                            <i class="ri-map-pin-line"></i> Esta dirección se usará para la entrega del premio en caso de resultar ganador(a)
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-3">
                                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                                data-nexttab="pills-order-tab" id="btn_continuar_personal" disabled>
                                                            <i class="ri-shopping-cart-line label-icon align-middle fs-16 ms-2"></i>
                                                            <span class="d-none d-sm-inline">Continuar a tu Orden</span>
                                                            <span class="d-inline d-sm-none">Continuar</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tab 2: Tu Orden -->
                                                <div class="tab-pane fade" id="pills-order" role="tabpanel">
                                                    <div>
                                                        <h5 class="mb-1">Información de tu Orden</h5>
                                                        <p class="text-muted mb-4">Selecciona la cantidad de tickets</p>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="mb-4">
                                                                <label for="cantidad_tickets" class="form-label">
                                                                    ¿Cuántos tickets deseas comprar? <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="input-group input-group-lg">
                                                                    <button class="btn btn-outline-secondary" type="button" id="btn_menos">
                                                                        <i class="ri-subtract-line"></i>
                                                                    </button>
                                                                    <input type="number" class="form-control text-center fs-20 fw-semibold" 
                                                                           id="cantidad_tickets" name="cantidad_tickets" value="1" min="1" max="999">
                                                                    <button class="btn btn-outline-secondary" type="button" id="btn_mas">
                                                                        <i class="ri-add-line"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="text-danger small mt-1" id="cantidad_tickets_error" style="display: none;"></div>
                                                                <small class="text-muted d-block mt-2">
                                                                    <i class="ri-information-line"></i> Mientras más tickets compres, mayores probabilidades de ganar
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Tickets Disponibles Info -->
                                                    <div class="alert alert-info border-0 mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ri-ticket-line fs-20 me-2"></i>
                                                            <div>
                                                                <strong>Tickets Disponibles:</strong> 
                                                                <span id="tickets_disponibles_tab">0</span> de <span id="tickets_total_tab">0</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- SISTEMA DE SELECCIÓN DE NÚMEROS DE BOLETO -->
                                                    <div class="card border border-success mb-4" id="seccion_seleccion_numero">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">
                                                                <i class="ri-hashtag text-success me-1"></i> Selecciona tu Número de la Suerte
                                                            </h6>
                                                            
                                                            <!-- Opciones de Selección -->
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-md-6">
                                                                    <div class="card bg-light border h-100">
                                                                        <div class="card-body text-center p-3">
                                                                            <div class="avatar-sm mx-auto mb-2">
                                                                                <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                                    <i class="ri-hashtag fs-18"></i>
                                                                                </div>
                                                                            </div>
                                                                            <h6 class="mb-2">¿Tienes un número favorito?</h6>
                                                                            <p class="text-muted small mb-3">Elige tu número de la suerte</p>
                                                                            <button type="button" class="btn btn-primary btn-sm" onclick="mostrarGridNumeros()">
                                                                                <i class="ri-search-line me-1"></i> Ver Números Disponibles
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-6">
                                                                    <div class="card bg-light border h-100">
                                                                        <div class="card-body text-center p-3">
                                                                            <div class="avatar-sm mx-auto mb-2">
                                                                                <div class="avatar-title bg-success-subtle text-success rounded-circle">
                                                                                    <i class="ri-shuffle-line fs-18"></i>
                                                                                </div>
                                                                            </div>
                                                                            <h6 class="mb-2">¿Prefieres la sorpresa?</h6>
                                                                            <p class="text-muted small mb-3">Nosotros elegimos por ti</p>
                                                                            <button type="button" class="btn btn-success btn-sm" onclick="asignarNumerosAleatorios()">
                                                                                <i class="ri-refresh-line me-1"></i> Asignar Números Aleatorios
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Números Seleccionados/Reservados -->
                                                            <div id="numero_seleccionado_display" style="display: none;">
                                                                <div class="alert alert-warning border-0 mb-0">
                                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="ri-ticket-2-line fs-20 me-2"></i>
                                                                            <div>
                                                                                <strong>Tus números seleccionados:</strong>
                                                                                <span class="badge bg-info ms-2" id="contador_numeros">0/0</span>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button" class="btn btn-sm btn-light" onclick="cancelarTodasLasSelecciones()">
                                                                            <i class="ri-close-line"></i> Limpiar Todo
                                                                        </button>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap gap-2 mb-2" id="lista_numeros_seleccionados">
                                                                        <!-- Se llenará dinámicamente -->
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <small>
                                                                            <i class="ri-time-line me-1"></i> 
                                                                            Reservado por: <strong id="timer_reserva">10:00</strong>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <input type="hidden" id="numeros_reservados" name="numeros_reservados">
                                                            <input type="hidden" id="numeros_formateados" name="numeros_formateados">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="button" class="btn btn-light btn-label previestab" 
                                                                data-previous="pills-personal-tab">
                                                            <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                            <span class="d-none d-sm-inline">Volver a Información Personal</span>
                                                            <span class="d-inline d-sm-none">Volver</span>
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                                data-nexttab="pills-payment-tab" id="btn_continuar_orden">
                                                            <i class="ri-bank-card-line label-icon align-middle fs-16 ms-2"></i>
                                                            <span class="d-none d-sm-inline">Continuar a Pago</span>
                                                            <span class="d-inline d-sm-none">Continuar</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tab 3: Información de Pago -->
                                                <div class="tab-pane fade" id="pills-payment" role="tabpanel">
                                                    <div>
                                                        <h5 class="mb-1">Información de Pago</h5>
                                                        <p class="text-muted mb-4">Selecciona tu método de pago preferido</p>
                                                    </div>
                                                    
                                                    <h6 class="fs-14 mb-3">Métodos de Pago Disponibles</h6>
                                                    
                                                    <div class="row g-4">
                                                        <!-- Yape -->
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body text-center p-3">
                                                                    <div class="avatar-sm mx-auto mb-3">
                                                                        <div class="avatar-title bg-success-subtle text-success rounded-circle fs-20">
                                                                            <i class="ri-smartphone-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="mb-3">Yape</h6>
                                                                    <img src="assets/images/qr/yape-qr.png" alt="QR Yape" 
                                                                         class="img-fluid rounded border mb-2" 
                                                                         style="max-width: 120px; height: auto;"
                                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                    <div class="alert alert-secondary border-0 p-2" style="display: none;">
                                                                        <small><i class="ri-qr-code-line"></i> QR no disponible</small>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Número: <strong class="text-dark">519 873 862</strong>
                                                                    </p>
                                                                    <small class="text-success">
                                                                        <i class="ri-qr-scan-line"></i> Escanea el QR o usa el número
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Plin -->
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body text-center p-3">
                                                                    <div class="avatar-sm mx-auto mb-3">
                                                                        <div class="avatar-title bg-info-subtle text-info rounded-circle fs-20">
                                                                            <i class="ri-smartphone-line"></i>
                                                                        </div>
                                                                    </div>
                                                                    <h6 class="mb-3">Plin</h6>
                                                                    <img src="assets/images/qr/plin-qr.png" alt="QR Plin" 
                                                                         class="img-fluid rounded border mb-2" 
                                                                         style="max-width: 120px; height: auto;"
                                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                    <div class="alert alert-secondary border-0 p-2" style="display: none;">
                                                                        <small><i class="ri-qr-code-line"></i> QR no disponible</small>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Número: <strong class="text-dark">987 555 555</strong>
                                                                    </p>
                                                                    <small class="text-info">
                                                                        <i class="ri-qr-scan-line"></i> Escanea el QR o usa el número
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Interbank -->
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <div class="avatar-sm me-3">
                                                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                                <i class="ri-bank-line fs-18"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h6 class="mb-0">Banco Interbank</h6>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Cuenta: <strong class="text-dark">[Ingresar cuenta]</strong>
                                                                    </p>
                                                                    <p class="mb-0 small text-muted">
                                                                        CCI: <strong class="text-dark">[Ingresar CCI]</strong>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- BCP -->
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="card border shadow-none h-100">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-center mb-3">
                                                                        <div class="avatar-sm me-3">
                                                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                                                <i class="ri-bank-line fs-18"></i>
                                                                            </div>
                                                                        </div>
                                                                        <h6 class="mb-0">Banco BCP</h6>
                                                                    </div>
                                                                    <p class="mb-1 small text-muted">
                                                                        Cuenta: <strong class="text-dark">[Ingresar cuenta]</strong>
                                                                    </p>
                                                                    <p class="mb-0 small text-muted">
                                                                        CCI: <strong class="text-dark">[Ingresar CCI]</strong>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Subir Comprobante -->
                                                    <div class="mt-4">
                                                        <label for="comprobante_pago" class="form-label">
                                                            <i class="ri-image-add-line me-1"></i> Comprobante de Pago 
                                                            <span class="text-muted">(Opcional)</span>
                                                        </label>
                                                        <input type="file" class="form-control" id="comprobante_pago" 
                                                               name="comprobante_pago" accept="image/*,.pdf">
                                                        <div class="text-danger small mt-1" id="comprobante_pago_error" style="display: none;"></div>
                                                        <small class="text-muted d-block mt-2">
                                                            Si ya realizaste el pago, puedes subir tu comprobante ahora. Formatos: JPG, PNG, PDF (máx. 5MB)
                                                        </small>
                                                        
                                                        <!-- Vista previa -->
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
                                                    
                                                    <div class="alert alert-warning border-0 mt-3">
                                                        <small>
                                                            <i class="ri-information-line me-1"></i>
                                                            <strong>Importante:</strong> Una vez realizado el pago, sube tu comprobante o envíalo por WhatsApp para validar tu compra.
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="button" class="btn btn-light btn-label previestab" 
                                                                data-previous="pills-order-tab">
                                                            <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                            <span class="d-none d-sm-inline">Volver a tu Orden</span>
                                                            <span class="d-inline d-sm-none">Volver</span>
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-label right ms-auto nexttab" 
                                                                data-nexttab="pills-finish-tab" id="btn_continuar_pago">
                                                            <i class="ri-checkbox-circle-line label-icon align-middle fs-16 ms-2"></i>
                                                            <span class="d-none d-sm-inline">Revisar Orden</span>
                                                            <span class="d-inline d-sm-none">Continuar</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tab 4: Confirmar -->
                                                <div class="tab-pane fade" id="pills-finish" role="tabpanel">
                                                    <div class="text-center py-4">
                                                        <div class="mb-4">
                                                            <i class="ri-checkbox-circle-line text-success" style="font-size: 72px;"></i>
                                                        </div>
                                                        <h5 class="mb-2">Revisa tu Orden</h5>
                                                        <p class="text-muted">Verifica que toda la información sea correcta antes de confirmar</p>
                                                    </div>
                                                    
                                                    <!-- Resumen Final -->
                                                    <div class="card bg-light border-0 mb-4">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">Resumen de tu Compra</h6>
                                                            <div class="table-responsive">
                                                                <table class="table table-borderless mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-muted">Rifa:</td>
                                                                            <td class="text-end fw-semibold" id="resumen_rifa_nombre">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Participante:</td>
                                                                            <td class="text-end fw-semibold" id="resumen_nombre">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Email:</td>
                                                                            <td class="text-end" id="resumen_email">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Teléfono:</td>
                                                                            <td class="text-end" id="resumen_telefono">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Documento:</td>
                                                                            <td class="text-end" id="resumen_documento">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Dirección de Envío:</td>
                                                                            <td class="text-end" id="resumen_direccion">-</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Ciudad/Estado:</td>
                                                                            <td class="text-end" id="resumen_ubicacion">-</td>
                                                                        </tr>
                                                                        <tr id="resumen_numero_row" style="display: none;">
                                                                            <td class="text-muted">
                                                                                <i class="ri-hashtag me-1"></i>Números de Boleto:
                                                                            </td>
                                                                            <td class="text-end">
                                                                                <div class="d-flex flex-wrap gap-1 justify-content-end" id="resumen_numeros_boletos">
                                                                                    <!-- Se llenará dinámicamente -->
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Cantidad de Tickets:</td>
                                                                            <td class="text-end fw-semibold" id="resumen_cantidad">1</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-muted">Precio por Ticket:</td>
                                                                            <td class="text-end" id="resumen_precio">$0.00</td>
                                                                        </tr>
                                                                        <tr class="border-top">
                                                                            <td class="fw-semibold fs-15">Total a Pagar:</td>
                                                                            <td class="text-end fw-bold text-success fs-18" id="resumen_total">$0.00</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Términos y Condiciones -->
                                                    <div class="form-check mb-4">
                                                        <input class="form-check-input" type="checkbox" id="acepto_terminos" name="acepto_terminos">
                                                        <label class="form-check-label" for="acepto_terminos">
                                                            Acepto los <a href="terminos" target="_blank" class="text-primary">términos y condiciones</a> 
                                                            y las <a href="terminos" target="_blank" class="text-primary">bases del sorteo</a> 
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="text-danger small mt-1" id="acepto_terminos_error" style="display: none;"></div>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-start gap-3 mt-4">
                                                        <button type="button" class="btn btn-light btn-label previestab" 
                                                                data-previous="pills-payment-tab">
                                                            <i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i>
                                                            <span class="d-none d-sm-inline">Volver a Pago</span>
                                                            <span class="d-inline d-sm-none">Volver</span>
                                                        </button>
                                                        <button type="submit" class="btn btn-success btn-label right ms-auto" id="btn_realizar_compra" disabled>
                                                            <i class="ri-shopping-bag-line label-icon align-middle fs-16 ms-2"></i>
                                                            <span id="btn_compra_text" class="d-none d-sm-inline">Confirmar Compra</span>
                                                            <span id="btn_compra_text_mobile" class="d-inline d-sm-none">Confirmar</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <!-- end tab content -->
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Columna Derecha - Resumen de la Orden -->
                                <div class="col-xl-4">
                                    <div class="card">
                                        <div class="card-header bg-success-subtle border-0">
                                            <h5 class="card-title mb-0">
                                                <i class="ri-shopping-bag-line me-2"></i>Resumen de Orden
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            
                                            <!-- Premios -->
                                            <div class="mb-3">
                                                <h6 class="fs-14 mb-3">
                                                    <i class="ri-trophy-line text-warning me-1"></i> Premios de esta Rifa
                                                </h6>
                                                <div id="lista_premios" class="d-flex flex-column gap-2">
                                                    <!-- Se llenará dinámicamente -->
                                                </div>
                                            </div>
                                            
                                            <hr class="my-3">
                                            
                                            <!-- Detalles de Precio -->
                                            <div class="table-responsive table-card">
                                                <table class="table table-borderless align-middle mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted">Precio por Ticket:</td>
                                                            <td class="text-end fw-semibold">$<span id="precio_ticket">0.00</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Cantidad:</td>
                                                            <td class="text-end fw-semibold"><span id="cantidad_display">1</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Tickets Disponibles:</td>
                                                            <td class="text-end">
                                                                <span class="badge bg-primary-subtle text-primary">
                                                                    <span id="tickets_disponibles">0</span>/<span id="tickets_total">0</span>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr class="table-active">
                                                            <th class="fs-15">Total:</th>
                                                            <th class="text-end">
                                                                <span class="fw-semibold text-success fs-18">
                                                                    $<span id="total_pagar">0.00</span>
                                                                </span>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="alert alert-success border-0 mt-3 mb-0">
                                                <small class="mb-0">
                                                    <i class="ri-information-line me-1"></i>
                                                    Recibirás un correo de confirmación al completar tu compra
                                                </small>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end modal -->

        <!-- Modal para Seleccionar Número de Boleto -->
        <div class="modal fade" id="modal_seleccionar_numero" tabindex="-1" aria-labelledby="modal_seleccionar_numero_label" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary-subtle border-0">
                        <h5 class="modal-title" id="modal_seleccionar_numero_label">
                            <i class="ri-hashtag me-2"></i>Selecciona tu Número de la Suerte
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        
                        <!-- Buscador de Número -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                                    <input type="text" class="form-control" id="buscar_numero" 
                                           placeholder="Busca tu número favorito (ej: 777, 888, 1234...)">
                                    <button class="btn btn-primary" type="button" onclick="buscarNumero()">
                                        Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('todos')">
                                        Todos
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('pares')">
                                        Pares
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('impares')">
                                        Impares
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="filtrarNumeros('multiplos5')">
                                        Múltiplos de 5
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Estadísticas de Números -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">Disponibles</div>
                                        <h5 class="mb-0 text-success" id="stat_disponibles">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">Vendidos</div>
                                        <h5 class="mb-0 text-danger" id="stat_vendidos">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">Reservados</div>
                                        <h5 class="mb-0 text-warning" id="stat_reservados">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border mb-0">
                                    <div class="card-body p-2 text-center">
                                        <div class="text-muted small">% Vendido</div>
                                        <h5 class="mb-0 text-primary" id="stat_porcentaje">0%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Grid de Números -->
                        <div class="alert alert-info border-0 mb-3">
                            <small>
                                <i class="ri-information-line me-1"></i>
                                <strong>Leyenda:</strong> 
                                <span class="badge bg-success ms-2">Disponible</span>
                                <span class="badge bg-secondary ms-1">Vendido</span>
                                <span class="badge bg-warning ms-1">Reservado</span>
                                <span class="badge bg-dark ms-1">Bloqueado</span>
                            </small>
                        </div>
                        
                        <div class="card border">
                            <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
                                <div id="grid_numeros_disponibles" class="row g-2">
                                    <!-- Se llenará dinámicamente con JavaScript -->
                                    <div class="col-12 text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                        <p class="text-muted mt-2">Cargando números disponibles...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end modal seleccionar número -->

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
            let rifaNombreGlobal = '';

            // ======= NAVEGACIÓN ENTRE TABS =======
            
            // Prevenir navegación directa a tabs (solo permitir navegación por botones)
            document.querySelectorAll('#modal_comprar_ticket .nav-link').forEach(tabButton => {
                // Prevenir clic en tabs deshabilitados
                tabButton.addEventListener('click', function(e) {
                    if (this.disabled || this.hasAttribute('disabled')) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        return false;
                    }
                });
                
                // Prevenir evento show.bs.tab en tabs deshabilitados
                tabButton.addEventListener('show.bs.tab', function(e) {
                    if (this.disabled || this.hasAttribute('disabled')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            });
            
            // Botones de "Siguiente Tab"
            document.querySelectorAll('.nexttab').forEach(button => {
                button.addEventListener('click', function() {
                    const nextTabId = this.getAttribute('data-nexttab');
                    const nextTabButton = document.getElementById(nextTabId);
                    const currentTab = document.querySelector('#modal_comprar_ticket .nav-link.active');
                    
                    // Marcar el tab actual como completado
                    if (currentTab && !currentTab.classList.contains('done')) {
                        currentTab.classList.add('done');
                    }
                    
                    // Habilitar el siguiente tab antes de mostrarlo
                    nextTabButton.disabled = false;
                    nextTabButton.removeAttribute('disabled');
                    
                    const nextTab = new bootstrap.Tab(nextTabButton);
                    nextTab.show();
                    
                    // Scroll al inicio del modal
                    document.querySelector('#modal_comprar_ticket .modal-body').scrollTop = 0;
                });
            });

            // Botones de "Tab Anterior"
            document.querySelectorAll('.previestab').forEach(button => {
                button.addEventListener('click', function() {
                    const prevTabId = this.getAttribute('data-previous');
                    const prevTab = new bootstrap.Tab(document.getElementById(prevTabId));
                    prevTab.show();
                    
                    // Scroll al inicio del modal
                    document.querySelector('#modal_comprar_ticket .modal-body').scrollTop = 0;
                });
            });

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

                // Guardar nombre de rifa globalmente
                rifaNombreGlobal = rifaNombre;

                // Actualizar el modal
                document.getElementById('modal_titulo_rifa').textContent = rifaNombre;
                document.getElementById('rifa_id').value = rifaId;
                document.getElementById('precio_ticket').textContent = rifaPrecio;
                document.getElementById('tickets_disponibles').textContent = rifaDisponibles;
                document.getElementById('tickets_total').textContent = rifaTotal;
                
                // Actualizar tabs duplicados
                document.getElementById('tickets_disponibles_tab').textContent = rifaDisponibles;
                document.getElementById('tickets_total_tab').textContent = rifaTotal;

                // Guardar valores para cálculos
                precioUnitario = parseFloat(rifaPrecio);
                ticketsDisponibles = parseInt(rifaDisponibles);

                // Actualizar límite máximo de tickets
                document.getElementById('cantidad_tickets').setAttribute('max', rifaDisponibles);

                // Mostrar lista de premios
                const listaPremios = document.getElementById('lista_premios');
                listaPremios.innerHTML = '';
                
                rifaPremios.forEach((premio, index) => {
                    const iconClass = index === 0 ? 'ri-trophy-fill text-warning' : 
                                     index === 1 ? 'ri-medal-line text-secondary' : 
                                     'ri-award-line text-dark';
                    listaPremios.innerHTML += `
                        <div class="d-flex align-items-center gap-2 small">
                            <i class="${iconClass} fs-16"></i>
                            <span class="text-muted">${premio.posicion}°</span>
                            <strong>${premio.nombre}</strong>
                        </div>
                    `;
                });

                // Resetear cantidad a 1
                document.getElementById('cantidad_tickets').value = 1;
                calcularTotal();
                
                // Resetear navegación de tabs - Solo el primero habilitado
                const personalTab = document.getElementById('pills-personal-tab');
                const orderTab = document.getElementById('pills-order-tab');
                const paymentTab = document.getElementById('pills-payment-tab');
                const finishTab = document.getElementById('pills-finish-tab');
                
                personalTab.disabled = false;
                personalTab.removeAttribute('disabled');
                personalTab.classList.remove('done');
                
                orderTab.disabled = true;
                orderTab.setAttribute('disabled', 'disabled');
                orderTab.classList.remove('done');
                
                paymentTab.disabled = true;
                paymentTab.setAttribute('disabled', 'disabled');
                paymentTab.classList.remove('done');
                
                finishTab.disabled = true;
                finishTab.setAttribute('disabled', 'disabled');
                finishTab.classList.remove('done');
                
                // Volver al primer tab
                const firstTab = new bootstrap.Tab(personalTab);
                firstTab.show();
            });

            // Función para calcular el total
            function calcularTotal() {
                const cantidad = parseInt(document.getElementById('cantidad_tickets').value) || 1;
                const total = (cantidad * precioUnitario).toFixed(2);
                
                document.getElementById('total_pagar').textContent = total;
                document.getElementById('cantidad_display').textContent = cantidad;
            }
            
            // Función para actualizar el resumen final
            function actualizarResumenFinal() {
                const ciudad = document.getElementById('ciudad').value || '-';
                const estado = document.getElementById('estado').value || '-';
                const ubicacion = ciudad !== '-' && estado !== '-' ? `${ciudad}, ${estado}` : '-';
                
                // Documento
                const tipoDoc = document.getElementById('tipo_documento').value || '';
                const numDoc = document.getElementById('numero_documento').value || '';
                const documento = tipoDoc && numDoc ? `${tipoDoc}: ${numDoc}` : '-';
                
                // Números de boleto seleccionados
                const numerosReservadosJSON = document.getElementById('numeros_reservados').value;
                const numerosFormateadosJSON = document.getElementById('numeros_formateados').value;
                
                document.getElementById('resumen_rifa_nombre').textContent = rifaNombreGlobal;
                document.getElementById('resumen_nombre').textContent = document.getElementById('nombre_completo').value || '-';
                document.getElementById('resumen_email').textContent = document.getElementById('email_participante').value || '-';
                document.getElementById('resumen_telefono').textContent = document.getElementById('telefono').value || '-';
                document.getElementById('resumen_documento').textContent = documento;
                document.getElementById('resumen_direccion').textContent = document.getElementById('direccion_envio').value || '-';
                document.getElementById('resumen_ubicacion').textContent = ubicacion;
                
                // Mostrar números de boleto si se seleccionaron
                if (numerosFormateadosJSON && numerosFormateadosJSON !== '') {
                    try {
                        const numerosArray = JSON.parse(numerosFormateadosJSON);
                        const containerNumeros = document.getElementById('resumen_numeros_boletos');
                        containerNumeros.innerHTML = '';
                        
                        numerosArray.forEach(num => {
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-success fs-14 px-3 py-2';
                            badge.textContent = num;
                            containerNumeros.appendChild(badge);
                        });
                        
                        document.getElementById('resumen_numero_row').style.display = '';
                    } catch (e) {
                        console.error('Error parsing números:', e);
                        document.getElementById('resumen_numero_row').style.display = 'none';
                    }
                } else {
                    document.getElementById('resumen_numero_row').style.display = 'none';
                }
                
                document.getElementById('resumen_cantidad').textContent = document.getElementById('cantidad_tickets').value;
                document.getElementById('resumen_precio').textContent = '$' + precioUnitario.toFixed(2);
                document.getElementById('resumen_total').textContent = '$' + document.getElementById('total_pagar').textContent;
            }
            
            // Actualizar resumen cuando se llega al tab final
            document.getElementById('pills-finish-tab').addEventListener('shown.bs.tab', function() {
                actualizarResumenFinal();
            });
            
            // ======= VALIDACIÓN EN TIEMPO REAL Y HABILITACIÓN DE BOTONES =======
            
            // Función para validar Tab 1 - Información Personal
            function validarTabPersonal() {
                const nombreInput = document.getElementById('nombre_completo');
                const emailInput = document.getElementById('email_participante');
                const telefonoInput = document.getElementById('telefono');
                const ciudadInput = document.getElementById('ciudad');
                const estadoInput = document.getElementById('estado');
                const direccionInput = document.getElementById('direccion_envio');
                
                const nombreCompleto = nombreInput.value.trim();
                const email = emailInput.value.trim();
                const telefono = telefonoInput.value.trim();
                const ciudad = ciudadInput.value.trim();
                const estado = estadoInput.value.trim();
                const direccion = direccionInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                const nombreValido = nombreCompleto.length >= 3;
                const emailValido = emailRegex.test(email);
                const telefonoValido = telefono.length >= 8;
                const ciudadValida = ciudad.length >= 3;
                const estadoValido = estado.length >= 3;
                const direccionValida = direccion.length >= 10;
                
                // Feedback visual para nombre
                if (nombreCompleto.length > 0) {
                    if (nombreValido) {
                        nombreInput.classList.remove('border-danger');
                        nombreInput.classList.add('border-success');
                    } else {
                        nombreInput.classList.remove('border-success');
                    }
                } else {
                    nombreInput.classList.remove('border-success', 'border-danger');
                }
                
                // Feedback visual para email
                if (email.length > 0) {
                    if (emailValido) {
                        emailInput.classList.remove('border-danger');
                        emailInput.classList.add('border-success');
                    } else {
                        emailInput.classList.remove('border-success');
                        emailInput.classList.add('border-danger');
                    }
                } else {
                    emailInput.classList.remove('border-success', 'border-danger');
                }
                
                // Feedback visual para teléfono
                if (telefono.length > 0) {
                    if (telefonoValido) {
                        telefonoInput.classList.remove('border-danger');
                        telefonoInput.classList.add('border-success');
                    } else {
                        telefonoInput.classList.remove('border-success');
                    }
                } else {
                    telefonoInput.classList.remove('border-success', 'border-danger');
                }
                
                // Feedback visual para ciudad
                if (ciudad.length > 0) {
                    if (ciudadValida) {
                        ciudadInput.classList.remove('border-danger');
                        ciudadInput.classList.add('border-success');
                    } else {
                        ciudadInput.classList.remove('border-success');
                    }
                } else {
                    ciudadInput.classList.remove('border-success', 'border-danger');
                }
                
                // Feedback visual para estado
                if (estado.length > 0) {
                    if (estadoValido) {
                        estadoInput.classList.remove('border-danger');
                        estadoInput.classList.add('border-success');
                    } else {
                        estadoInput.classList.remove('border-success');
                    }
                } else {
                    estadoInput.classList.remove('border-success', 'border-danger');
                }
                
                // Feedback visual para dirección
                if (direccion.length > 0) {
                    if (direccionValida) {
                        direccionInput.classList.remove('border-danger');
                        direccionInput.classList.add('border-success');
                    } else {
                        direccionInput.classList.remove('border-success');
                    }
                } else {
                    direccionInput.classList.remove('border-success', 'border-danger');
                }
                
                const esValido = nombreValido && emailValido && telefonoValido && ciudadValida && estadoValido && direccionValida;
                
                // Habilitar o deshabilitar botón
                const btnContinuar = document.getElementById('btn_continuar_personal');
                btnContinuar.disabled = !esValido;
                
                // Cambiar el texto del botón si está deshabilitado (responsive)
                if (!esValido) {
                    btnContinuar.innerHTML = `
                        <i class="ri-shopping-cart-line label-icon align-middle fs-16 ms-2"></i>
                        <span class="d-none d-sm-inline">Complete los datos obligatorios</span>
                        <span class="d-inline d-sm-none">Complete datos</span>
                    `;
                } else {
                    btnContinuar.innerHTML = `
                        <i class="ri-shopping-cart-line label-icon align-middle fs-16 ms-2"></i>
                        <span class="d-none d-sm-inline">Continuar a tu Orden</span>
                        <span class="d-inline d-sm-none">Continuar</span>
                    `;
                }
                
                return esValido;
            }
            
            // Función para validar Tab 2 - Tu Orden (cantidad de tickets)
            function validarTabOrden() {
                const cantidad = parseInt(document.getElementById('cantidad_tickets').value);
                const esValido = cantidad >= 1 && cantidad <= ticketsDisponibles;
                
                // Este tab siempre tiene un valor válido por defecto, pero validamos por si acaso
                document.getElementById('btn_continuar_orden').disabled = !esValido;
                
                return esValido;
            }
            
            // Función para validar Tab 4 - Confirmar (términos y condiciones)
            function validarTabConfirmar() {
                const aceptoTerminos = document.getElementById('acepto_terminos').checked;
                
                // Habilitar o deshabilitar botón de compra
                const btnCompra = document.getElementById('btn_realizar_compra');
                btnCompra.disabled = !aceptoTerminos;
                
                // Cambiar el texto del botón si está deshabilitado (desktop y móvil)
                const btnTextDesktop = document.getElementById('btn_compra_text');
                const btnTextMobile = document.getElementById('btn_compra_text_mobile');
                
                if (!aceptoTerminos) {
                    btnTextDesktop.textContent = 'Acepta los términos para continuar';
                    btnTextMobile.textContent = 'Acepta términos';
                } else {
                    btnTextDesktop.textContent = 'Confirmar Compra';
                    btnTextMobile.textContent = 'Confirmar';
                }
                
                return aceptoTerminos;
            }
            
            // ======= EVENTOS DE VALIDACIÓN EN TIEMPO REAL =======
            
            // Validar campos del Tab 1 en tiempo real
            document.getElementById('nombre_completo').addEventListener('input', function() {
                validarTabPersonal();
            });
            
            document.getElementById('email_participante').addEventListener('input', function() {
                validarTabPersonal();
            });
            
            document.getElementById('telefono').addEventListener('input', function() {
                validarTabPersonal();
            });
            
            document.getElementById('ciudad').addEventListener('input', function() {
                validarTabPersonal();
            });
            
            document.getElementById('estado').addEventListener('input', function() {
                validarTabPersonal();
            });
            
            document.getElementById('direccion_envio').addEventListener('input', function() {
                validarTabPersonal();
            });
            
            // Validar cantidad de tickets en tiempo real
            document.getElementById('cantidad_tickets').addEventListener('input', function() {
                validarTabOrden();
            });
            
            // Validar checkbox de términos
            document.getElementById('acepto_terminos').addEventListener('change', function() {
                validarTabConfirmar();
            });

            // Botón aumentar cantidad
            document.getElementById('btn_mas').addEventListener('click', function() {
                const input = document.getElementById('cantidad_tickets');
                let valor = parseInt(input.value) || 1;
                const max = parseInt(input.getAttribute('max'));
                
                if (valor < max) {
                    input.value = valor + 1;
                    calcularTotal();
                    validarTabOrden();
                }
            });

            // Botón disminuir cantidad
            document.getElementById('btn_menos').addEventListener('click', function() {
                const input = document.getElementById('cantidad_tickets');
                let valor = parseInt(input.value) || 1;
                
                if (valor > 1) {
                    input.value = valor - 1;
                    calcularTotal();
                    validarTabOrden();
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

                // Validar tipo de documento
                const tipoDocumento = document.getElementById('tipo_documento').value;
                if (tipoDocumento === '') {
                    mostrarError('tipo_documento', 'Por favor, seleccione el tipo de documento');
                    esValido = false;
                }

                // Validar número de documento
                const numeroDocumento = document.getElementById('numero_documento').value.trim();
                if (numeroDocumento === '') {
                    mostrarError('numero_documento', 'Por favor, ingrese su número de documento');
                    esValido = false;
                } else if (numeroDocumento.length < 6) {
                    mostrarError('numero_documento', 'El número de documento debe tener al menos 6 caracteres');
                    esValido = false;
                }

                // Validar ciudad
                const ciudad = document.getElementById('ciudad').value.trim();
                if (ciudad === '') {
                    mostrarError('ciudad', 'Por favor, ingrese su ciudad');
                    esValido = false;
                }

                // Validar estado
                const estado = document.getElementById('estado').value.trim();
                if (estado === '') {
                    mostrarError('estado', 'Por favor, ingrese su estado o provincia');
                    esValido = false;
                }

                // Validar dirección de envío
                const direccion = document.getElementById('direccion_envio').value.trim();
                if (direccion === '') {
                    mostrarError('direccion_envio', 'Por favor, ingrese su dirección completa de envío');
                    esValido = false;
                } else if (direccion.length < 10) {
                    mostrarError('direccion_envio', 'La dirección debe ser más específica (mínimo 10 caracteres)');
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

            // Limpiar error cuando el usuario empieza a escribir (inputs y textareas)
            document.querySelectorAll('#form_comprar_ticket input, #form_comprar_ticket textarea').forEach(input => {
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
                formData.append('estado', document.getElementById('estado').value);
                formData.append('direccion_envio', document.getElementById('direccion_envio').value);
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
                    estado: document.getElementById('estado').value,
                    direccion_envio: document.getElementById('direccion_envio').value,
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
                
                // Resetear navegación - solo primer tab habilitado
                const personalTab = document.getElementById('pills-personal-tab');
                const orderTab = document.getElementById('pills-order-tab');
                const paymentTab = document.getElementById('pills-payment-tab');
                const finishTab = document.getElementById('pills-finish-tab');
                
                personalTab.disabled = false;
                personalTab.removeAttribute('disabled');
                personalTab.classList.remove('done');
                
                orderTab.disabled = true;
                orderTab.setAttribute('disabled', 'disabled');
                orderTab.classList.remove('done');
                
                paymentTab.disabled = true;
                paymentTab.setAttribute('disabled', 'disabled');
                paymentTab.classList.remove('done');
                
                finishTab.disabled = true;
                finishTab.setAttribute('disabled', 'disabled');
                finishTab.classList.remove('done');
                
                // Deshabilitar botones de continuar
                document.getElementById('btn_continuar_personal').disabled = true;
                document.getElementById('btn_continuar_orden').disabled = false;
                document.getElementById('btn_continuar_pago').disabled = false;
                document.getElementById('btn_realizar_compra').disabled = true;
                
                // Volver al primer tab
                const firstTab = new bootstrap.Tab(personalTab);
                firstTab.show();
            });
            
            // Validar estado inicial cuando se muestran los tabs
            document.getElementById('pills-personal-tab').addEventListener('shown.bs.tab', function() {
                validarTabPersonal();
            });
            
            document.getElementById('pills-order-tab').addEventListener('shown.bs.tab', function() {
                validarTabOrden();
            });
            
            document.getElementById('pills-finish-tab').addEventListener('shown.bs.tab', function() {
                validarTabConfirmar();
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

    <!-- Estilos inline para Grid de Números -->
    <style>
        #grid_numeros_disponibles .numero-btn {
            padding: 12px 8px;
            font-size: 14px;
            font-weight: bold;
            border: 2px solid;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            display: block;
            width: 100%;
        }
        
        #grid_numeros_disponibles .numero-disponible {
            border-color: #28a745;
            background: white;
            color: #28a745;
        }
        
        #grid_numeros_disponibles .numero-disponible:hover {
            background: #28a745;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        
        #grid_numeros_disponibles .numero-vendido {
            border-color: #6c757d;
            background: #e9ecef;
            color: #6c757d;
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        #grid_numeros_disponibles .numero-reservado {
            border-color: #ffc107;
            background: #fff3cd;
            color: #856404;
            cursor: not-allowed;
        }
        
        #grid_numeros_disponibles .numero-bloqueado {
            border-color: #343a40;
            background: #f8f9fa;
            color: #343a40;
            cursor: not-allowed;
        }
        
        #grid_numeros_disponibles .numero-especial {
            border-color: #17a2b8;
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            box-shadow: 0 0 10px rgba(23, 162, 184, 0.5);
        }
        
        #grid_numeros_disponibles .numero-especial:hover {
            transform: scale(1.1);
        }
    </style>

    <!-- Script para Sistema de Selección de Números de Boleto -->
    <script>
        // Variables globales para gestión de números
        let numerosDisponibles = [];
        let numerosSeleccionados = []; // Ahora es un array de múltiples números
        let timerReserva = null;
        let tiempoRestante = 600; // 10 minutos en segundos
        let rifaActual = null;
        let cantidadTicketsRequerida = 1;
        
        // Función para mostrar modal con grid de números
        function mostrarGridNumeros() {
            const rifaId = document.getElementById('rifa_id').value;
            const cantidadTickets = parseInt(document.getElementById('cantidad_tickets').value) || 1;
            
            rifaActual = rifaId;
            cantidadTicketsRequerida = cantidadTickets;
            
            // Actualizar título del modal
            const tituloModal = document.getElementById('modal_seleccionar_numero_label');
            if (cantidadTickets > 1) {
                tituloModal.innerHTML = `<i class="ri-hashtag me-2"></i>Selecciona ${cantidadTickets} Números de la Suerte`;
            } else {
                tituloModal.innerHTML = `<i class="ri-hashtag me-2"></i>Selecciona tu Número de la Suerte`;
            }
            
            // Abrir modal
            const modal = new bootstrap.Modal(document.getElementById('modal_seleccionar_numero'));
            modal.show();
            
            // Cargar números disponibles
            cargarNumerosDisponibles(rifaId);
        }
        
        // Cargar números desde la API (simulado por ahora)
        async function cargarNumerosDisponibles(rifaId) {
            const gridContainer = document.getElementById('grid_numeros_disponibles');
            gridContainer.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="text-muted mt-2">Cargando números disponibles...</p></div>';
            
            // SIMULACIÓN - En producción esto vendría de: /api/rifas/{rifaId}/numeros-disponibles
            // TODO: Reemplazar con llamada real a API
            setTimeout(() => {
                // Simulación de datos
                const numerosSimulados = generarNumerosSimulados(1, 100, rifaId);
                mostrarGridNumeros_Render(numerosSimulados);
            }, 500);
        }
        
        // Generar números simulados (TEMPORAL - reemplazar con API real)
        function generarNumerosSimulados(inicio, fin, rifaId) {
            const numeros = [];
            for (let i = inicio; i <= fin; i++) {
                // Simular algunos vendidos/reservados
                let estado = 'DISPONIBLE';
                if (i % 13 === 0) estado = 'VENDIDO';
                else if (i % 17 === 0) estado = 'RESERVADO';
                else if (i % 37 === 0) estado = 'BLOQUEADO';
                
                numeros.push({
                    numero_entero: i,
                    numero_formateado: String(i).padStart(4, '0'),
                    estado: estado,
                    es_especial: (i % 50 === 0) // 50, 100, 150 son especiales
                });
            }
            numerosDisponibles = numeros;
            return numeros;
        }
        
        // Renderizar grid de números
        function mostrarGridNumeros_Render(numeros) {
            const gridContainer = document.getElementById('grid_numeros_disponibles');
            gridContainer.innerHTML = '';
            
            // Calcular estadísticas
            const disponibles = numeros.filter(n => n.estado === 'DISPONIBLE').length;
            const vendidos = numeros.filter(n => n.estado === 'VENDIDO').length;
            const reservados = numeros.filter(n => n.estado === 'RESERVADO').length;
            const porcentaje = Math.round((vendidos / numeros.length) * 100);
            
            document.getElementById('stat_disponibles').textContent = disponibles;
            document.getElementById('stat_vendidos').textContent = vendidos;
            document.getElementById('stat_reservados').textContent = reservados;
            document.getElementById('stat_porcentaje').textContent = porcentaje + '%';
            
            // Renderizar números en grid (Bootstrap col)
            numeros.forEach(num => {
                const col = document.createElement('div');
                col.className = 'col-lg-1 col-md-2 col-sm-3 col-4';
                
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = num.numero_formateado;
                button.dataset.numero = num.numero_entero;
                button.dataset.formateado = num.numero_formateado;
                button.dataset.estado = num.estado;
                
                // Clases según estado
                if (num.estado === 'VENDIDO') {
                    button.className = 'numero-btn numero-vendido';
                    button.disabled = true;
                } else if (num.estado === 'RESERVADO') {
                    button.className = 'numero-btn numero-reservado';
                    button.disabled = true;
                } else if (num.estado === 'BLOQUEADO') {
                    button.className = 'numero-btn numero-bloqueado';
                    button.disabled = true;
                } else if (num.es_especial) {
                    button.className = 'numero-btn numero-especial numero-disponible';
                    button.onclick = () => seleccionarNumero(num.numero_entero, num.numero_formateado);
                } else {
                    button.className = 'numero-btn numero-disponible';
                    button.onclick = () => seleccionarNumero(num.numero_entero, num.numero_formateado);
                }
                
                col.appendChild(button);
                gridContainer.appendChild(col);
            });
        }
        
        // Seleccionar número específico
        async function seleccionarNumero(numeroEntero, numeroFormateado) {
            // Verificar si ya está seleccionado
            const yaSeleccionado = numerosSeleccionados.find(n => n.entero === numeroEntero);
            if (yaSeleccionado) {
                mostrarNotificacion('Este número ya fue seleccionado', 'warning');
                return;
            }
            
            // Verificar si ya completó la cantidad requerida
            if (numerosSeleccionados.length >= cantidadTicketsRequerida) {
                mostrarNotificacion(`Solo puedes seleccionar ${cantidadTicketsRequerida} número(s)`, 'warning');
                return;
            }
            
            const rifaId = document.getElementById('rifa_id').value;
            const sesionId = obtenerOGenerarSesionId();
            
            // SIMULACIÓN - En producción: POST /api/rifas/reservar-numero
            console.log('Reservando número:', {rifaId, numeroEntero, sesionId});
            
            // Simulación de respuesta exitosa
            setTimeout(() => {
                const result = {exito: true, mensaje: 'Número reservado exitosamente'};
                
                if (result.exito) {
                    // Agregar a la lista de seleccionados
                    numerosSeleccionados.push({
                        entero: numeroEntero,
                        formateado: numeroFormateado
                    });
                    
                    // Actualizar display
                    actualizarDisplayNumeros();
                    
                    // Si ya completó la cantidad, cerrar modal
                    if (numerosSeleccionados.length >= cantidadTicketsRequerida) {
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modal_seleccionar_numero'));
                            if (modal) modal.hide();
                        }, 500);
                    }
                    
                    // Iniciar temporizador si es el primer número
                    if (numerosSeleccionados.length === 1) {
                        iniciarTemporizadorReserva();
                    }
                    
                    // Mostrar notificación
                    mostrarNotificacion(`Número ${numeroFormateado} reservado (${numerosSeleccionados.length}/${cantidadTicketsRequerida})`, 'success');
                } else {
                    alert(result.mensaje);
                }
            }, 300);
        }
        
        // Actualizar el display de números seleccionados
        function actualizarDisplayNumeros() {
            const display = document.getElementById('numero_seleccionado_display');
            const lista = document.getElementById('lista_numeros_seleccionados');
            const contador = document.getElementById('contador_numeros');
            
            if (numerosSeleccionados.length === 0) {
                display.style.display = 'none';
                return;
            }
            
            // Mostrar display
            display.style.display = 'block';
            
            // Actualizar contador
            contador.textContent = `${numerosSeleccionados.length}/${cantidadTicketsRequerida}`;
            
            // Actualizar lista de badges
            lista.innerHTML = '';
            numerosSeleccionados.forEach((num, index) => {
                const badge = document.createElement('div');
                badge.className = 'd-inline-flex align-items-center';
                badge.innerHTML = `
                    <span class="badge bg-success fs-16 px-3 py-2 me-1">${num.formateado}</span>
                    <button type="button" class="btn btn-sm btn-danger p-1" onclick="eliminarNumero(${index})" style="width: 24px; height: 24px; line-height: 1;">
                        <i class="ri-close-line" style="font-size: 12px;"></i>
                    </button>
                `;
                lista.appendChild(badge);
            });
            
            // Actualizar campos ocultos
            const enterosArray = numerosSeleccionados.map(n => n.entero);
            const formateadosArray = numerosSeleccionados.map(n => n.formateado);
            
            document.getElementById('numeros_reservados').value = JSON.stringify(enterosArray);
            document.getElementById('numeros_formateados').value = JSON.stringify(formateadosArray);
        }
        
        // Eliminar un número específico de la selección
        function eliminarNumero(index) {
            if (confirm('¿Deseas eliminar este número de tu selección?')) {
                numerosSeleccionados.splice(index, 1);
                actualizarDisplayNumeros();
                
                // Si no quedan números, detener temporizador
                if (numerosSeleccionados.length === 0) {
                    if (timerReserva) {
                        clearInterval(timerReserva);
                        timerReserva = null;
                    }
                }
            }
        }
        
        // Asignar múltiples números aleatorios
        async function asignarNumerosAleatorios() {
            const rifaId = document.getElementById('rifa_id').value;
            const cantidadTickets = parseInt(document.getElementById('cantidad_tickets').value) || 1;
            const sesionId = obtenerOGenerarSesionId();
            
            // SIMULACIÓN - En producción: POST /api/rifas/numeros-aleatorios
            console.log('Solicitando números aleatorios:', {rifaId, cantidad: cantidadTickets, sesionId});
            
            // Simulación de asignación aleatoria
            setTimeout(() => {
                // Buscar números disponibles
                const numerosDisp = numerosDisponibles.filter(n => n.estado === 'DISPONIBLE');
                
                if (numerosDisp.length < cantidadTickets) {
                    alert(`No hay suficientes números disponibles. Solo hay ${numerosDisp.length} disponibles.`);
                    return;
                }
                
                // Limpiar selección anterior
                numerosSeleccionados = [];
                
                // Seleccionar números aleatorios
                const numerosUsados = [];
                for (let i = 0; i < cantidadTickets; i++) {
                    let numeroAleatorio;
                    do {
                        const randomIndex = Math.floor(Math.random() * numerosDisp.length);
                        numeroAleatorio = numerosDisp[randomIndex];
                    } while (numerosUsados.includes(numeroAleatorio.numero_entero));
                    
                    numerosUsados.push(numeroAleatorio.numero_entero);
                    numerosSeleccionados.push({
                        entero: numeroAleatorio.numero_entero,
                        formateado: numeroAleatorio.numero_formateado
                    });
                }
                
                // Actualizar display
                cantidadTicketsRequerida = cantidadTickets;
                actualizarDisplayNumeros();
                
                // Iniciar temporizador
                iniciarTemporizadorReserva();
                
                // Mostrar notificación
                const numerosTexto = numerosSeleccionados.map(n => n.formateado).join(', ');
                mostrarNotificacion(`Se te asignaron los números: ${numerosTexto}`, 'success');
            }, 300);
        }
        
        // Cancelar todas las selecciones
        function cancelarTodasLasSelecciones() {
            if (confirm('¿Estás seguro de que quieres limpiar todos los números seleccionados?')) {
                // Limpiar selección
                numerosSeleccionados = [];
                
                // Limpiar campos ocultos
                document.getElementById('numeros_reservados').value = '';
                document.getElementById('numeros_formateados').value = '';
                
                // Ocultar display
                document.getElementById('numero_seleccionado_display').style.display = 'none';
                
                // Detener temporizador
                if (timerReserva) {
                    clearInterval(timerReserva);
                    timerReserva = null;
                }
                
                // TODO: En producción, liberar los números en el backend
            }
        }
        
        // Iniciar temporizador de reserva (10 minutos)
        function iniciarTemporizadorReserva() {
            tiempoRestante = 600; // 10 minutos
            
            // Limpiar temporizador anterior si existe
            if (timerReserva) {
                clearInterval(timerReserva);
            }
            
            // Actualizar cada segundo
            timerReserva = setInterval(() => {
                tiempoRestante--;
                
                // Formatear tiempo
                const minutos = Math.floor(tiempoRestante / 60);
                const segundos = tiempoRestante % 60;
                const tiempoFormateado = minutos + ':' + String(segundos).padStart(2, '0');
                
                document.getElementById('timer_reserva').textContent = tiempoFormateado;
                
                // Si el tiempo se agotó
                if (tiempoRestante <= 0) {
                    clearInterval(timerReserva);
                    alert('⏰ Tu reserva ha expirado. Por favor, selecciona otro número.');
                    cancelarSeleccionNumero();
                    location.reload();
                }
                
                // Alerta a los 2 minutos
                if (tiempoRestante === 120) {
                    mostrarNotificacion('⚠️ Solo quedan 2 minutos de tu reserva', 'warning');
                }
            }, 1000);
        }
        
        // Buscar número específico
        function buscarNumero() {
            const busqueda = document.getElementById('buscar_numero').value.trim();
            if (busqueda === '') {
                mostrarNotificacion('Ingresa un número para buscar', 'warning');
                return;
            }
            
            const numeroEncontrado = numerosDisponibles.find(n => 
                n.numero_formateado.includes(busqueda) || 
                String(n.numero_entero) === busqueda
            );
            
            if (!numeroEncontrado) {
                mostrarNotificacion('No se encontró el número: ' + busqueda, 'error');
                return;
            }
            
            // Hacer scroll al número
            const grid = document.getElementById('grid_numeros_disponibles');
            const buttons = grid.querySelectorAll('button');
            buttons.forEach(btn => {
                if (btn.dataset.numero == numeroEncontrado.numero_entero) {
                    btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    btn.classList.add('animate__animated', 'animate__pulse');
                    setTimeout(() => {
                        btn.classList.remove('animate__animated', 'animate__pulse');
                    }, 1000);
                }
            });
        }
        
        // Filtrar números
        function filtrarNumeros(filtro) {
            let numerosFiltrados = [...numerosDisponibles];
            
            if (filtro === 'pares') {
                numerosFiltrados = numerosFiltrados.filter(n => n.numero_entero % 2 === 0);
            } else if (filtro === 'impares') {
                numerosFiltrados = numerosFiltrados.filter(n => n.numero_entero % 2 !== 0);
            } else if (filtro === 'multiplos5') {
                numerosFiltrados = numerosFiltrados.filter(n => n.numero_entero % 5 === 0);
            }
            
            mostrarGridNumeros_Render(numerosFiltrados);
        }
        
        // Obtener o generar ID de sesión único
        function obtenerOGenerarSesionId() {
            let sesionId = sessionStorage.getItem('session_id_rifas');
            
            if (!sesionId) {
                sesionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('session_id_rifas', sesionId);
            }
            
            return sesionId;
        }
        
        // Mostrar notificación (usando alert por ahora, luego puede ser toast)
        function mostrarNotificacion(mensaje, tipo) {
            // TODO: Implementar sistema de toasts/notificaciones
            if (tipo === 'error') {
                alert('❌ ' + mensaje);
            } else if (tipo === 'warning') {
                alert('⚠️ ' + mensaje);
            } else {
                console.log('✅ ' + mensaje);
            }
        }
        
        // Actualizar resumen cuando se selecciona número
        document.addEventListener('DOMContentLoaded', function() {
            // Observar cambios en número seleccionado
            const observer = new MutationObserver(function(mutations) {
                const numeroDisplay = document.getElementById('numero_seleccionado_display');
                if (numeroDisplay && numeroDisplay.style.display !== 'none') {
                    // Actualizar resumen en Tab 4 si es necesario
                    const numeroTexto = document.getElementById('numero_elegido_text').textContent;
                    // Puede agregarse una fila en el resumen final para mostrar el número
                }
            });
        });
    </script>

    <!-- Script para Actualizar Resumen con Número de Boleto -->
    <script>
        // Modificar la función de envío para incluir campos de documento y números
        document.addEventListener('DOMContentLoaded', function() {
            // Observer para limpiar selecciones cuando se cambia la cantidad de tickets
            document.getElementById('cantidad_tickets').addEventListener('change', function() {
                // Si cambia la cantidad, limpiar números seleccionados
                if (numerosSeleccionados.length > 0) {
                    const nuevaCantidad = parseInt(this.value);
                    if (nuevaCantidad !== numerosSeleccionados.length) {
                        if (confirm('Al cambiar la cantidad de tickets se limpiarán los números seleccionados. ¿Continuar?')) {
                            cancelarTodasLasSelecciones();
                        } else {
                            this.value = numerosSeleccionados.length;
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>

