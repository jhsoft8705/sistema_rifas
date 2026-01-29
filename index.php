<?php
require_once __DIR__ . "/config/Enrutamiento.php";
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Sistema de Rifas - Participa y Gana</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema profesional de rifas y sorteos. Participa y gana increíbles premios." name="description" />
    <meta content="Sistema de Rifas" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/logos/logo.ico">
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
    <!-- Landing Rifas Theme CSS -->
    <link href="assets/css/landing-rifas.css" rel="stylesheet" type="text/css" />

 <!-- SweetAlert2 CSS --> 
<link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <nav class="navbar navbar-expand-lg navbar-landing fixed-top" id="navbar">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="">
                    <img src="assets/images/logos/logo.png" class="card-logo card-logo-dark" alt="logo dark" height="35">
                    <img src="assets/images/logos/logo.png" class="card-logo card-logo-light" alt="logo light" height="35">
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
                        <a href="admin-login" class="btn btn-primary"><i class="ri-admin-line me-1"></i>LogIn</a>
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

                <div class="row g-4" id="contenedor_rifas_publicas">
                    <!-- Las rifas se cargarán dinámicamente desde la API -->
                    <div class="col-12">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando rifas...</span>
                            </div>
                            <p class="text-muted mt-2">Cargando rifas disponibles...</p>
                        </div>
                    </div>
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

        <?php require_once __DIR__ . '/modals.php'; ?>

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

                <div class="row gy-4" id="contenedor_ganadores">
                    <!-- Se rellena por JS desde api/juegos/ganadoresPublicos; foto genérica: user-dummy-img.jpg -->
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
                                <a href="https://wa.me/" target="_blank" class="btn btn-success btn-whatsapp btn-label rounded-pill"><i class="ri-whatsapp-line label-icon align-middle rounded-pill fs-16 me-2"></i> WhatsApp</a>
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
                                <i class="ri-shield-keyhole-line fs-24 align-middle text-primary me-1"></i>
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
                            <form id="form_contacto" novalidate>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="contacto_nombre" class="form-label fs-13">Nombre <span class="text-danger">*</span></label>
                                            <input name="nombre" id="contacto_nombre" type="text" class="form-control bg-light border-light" placeholder="Tu nombre" required maxlength="200">
                                            <div class="invalid-feedback" id="contacto_nombre_error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="contacto_email" class="form-label fs-13">Correo <span class="text-danger">*</span></label>
                                            <input name="email" id="contacto_email" type="email" class="form-control bg-light border-light" placeholder="Tu correo" required maxlength="150">
                                            <div class="invalid-feedback" id="contacto_email_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="contacto_telefono" class="form-label fs-13">Teléfono</label>
                                            <input name="telefono" id="contacto_telefono" type="text" class="form-control bg-light border-light" placeholder="Teléfono (opcional)" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="contacto_asunto" class="form-label fs-13">Asunto <span class="text-danger">*</span></label>
                                            <input name="asunto" id="contacto_asunto" type="text" class="form-control bg-light border-light" placeholder="Asunto del mensaje" required maxlength="255">
                                            <div class="invalid-feedback" id="contacto_asunto_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label for="contacto_mensaje" class="form-label fs-13">Mensaje <span class="text-danger">*</span></label>
                                            <textarea name="mensaje" id="contacto_mensaje" rows="5" class="form-control bg-light border-light" placeholder="Tu mensaje..." required></textarea>
                                            <div class="invalid-feedback" id="contacto_mensaje_error"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-end">
                                        <button type="submit" class="btn btn-primary" id="btn_enviar_contacto">
                                            Enviar Mensaje <i class="ri-send-plane-fill align-middle ms-1"></i>
                                        </button>
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
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="mb-4">
                                                <label for="documento_ticket" class="form-label fs-13">
                                                    <i class="ri-file-text-line me-1"></i> Número de documento, código de ticket o uno de tus números
                                                </label>
                                                <input type="text" class="form-control bg-light border-light" id="documento_ticket" name="documento_ticket"
                                                    placeholder="Ej: 12345678, TICKET-2025-001 o R005-26">
                                                <div class="text-danger small mt-1" id="documento_ticket_error" style="display: none;"></div>
                                                <small class="text-muted">Ingresa tu DNI, código de ticket o uno de los números que compraste</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary" id="btn_consultar_tickets">
                                            <i class="ri-search-line me-1"></i> Consultar mis Tickets
                                        </button>
                                    </div>
                                </form>
                                <!-- Enlaces de contacto y transmisión (configurables) -->
                                <input type="hidden" id="config_whatsapp" value="999999999">
                                <input type="hidden" id="config_tiktok" value="https://www.tiktok.com/@sistemarifas">
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
    <!-- jQuery (debe cargarse primero) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
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
    <!-- Configuración de rutas y API para Landing -->
    <script>
        // Configurar rutas base desde PHP Enrutamiento
        window.BASE_URL = '<?= Enrutamiento::dominio() ?>';
        window.API_BASE_URL = window.BASE_URL + '/api';
        console.log('Rutas configuradas:', { BASE_URL: window.BASE_URL, API_BASE_URL: window.API_BASE_URL });
        console.log('BASE_URL:', window.BASE_URL);
        console.log('API_BASE_URL:', window.API_BASE_URL);
    </script>
    <!-- SweetAlert2 (debe cargarse antes de Utils.js) -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/pages/sweetalerts.init.js"></script>
    <!-- jQuery Toast Plugin (necesario para Utils.js) -->
    <script src="node_modules/jquery-toast-plugin/dist/jquery.toast.min.js" onerror="console.warn('jQuery Toast no se pudo cargar desde node_modules')"></script>
    <!-- Fallback: Si no existe en node_modules, usar CDN -->
    <script>
        // Esperar a que jQuery esté disponible antes de verificar
        (function() {
            function checkToastPlugin() {
                if (typeof $ !== 'undefined' && typeof $.toast === 'undefined') {
                    console.warn('jQuery Toast no está disponible, cargando desde CDN...');
                    const toastScript = document.createElement('script');
                    toastScript.src = 'https://cdn.jsdelivr.net/npm/jquery-toast-plugin@1.3.2/dist/jquery.toast.min.js';
                    toastScript.onerror = function() {
                        console.warn('No se pudo cargar jQuery Toast desde CDN');
                    };
                    document.head.appendChild(toastScript);
                } else if (typeof $ === 'undefined') {
                    // Si jQuery aún no está disponible, esperar un poco más
                    setTimeout(checkToastPlugin, 100);
                }
            }
            // Verificar después de que jQuery se haya cargado
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', checkToastPlugin);
            } else {
                setTimeout(checkToastPlugin, 100);
            }
        })();
    </script>
    <!-- Utils.js - Utilidades globales (requiere jQuery y SweetAlert2) -->
    <script src="<?= Enrutamiento::dominio() ?>/helpers/Utils.js"></script> 
    <!-- Landing.js - Gestión dinámica de rifas (requiere Utils.js) -->
    <script src="<?= Enrutamiento::dominio() ?>/landing.js"></script>
</body>

</html>
         

 
 

