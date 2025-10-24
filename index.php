<?php
require_once __DIR__ . '/config/Enrutamiento.php';
$base_url = Enrutamiento::dominio();
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">

<head>

    <meta charset="utf-8" />
    <title>Sign In | Cafed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sistema de planillas" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

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
    <!-- jQuery Toast Plugin CSS -->
    <link href="node_modules/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css" />

    <!-- SweetAlert2 CSS -->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="index.html" class="d-block">
                                                    <img src="assets/images/logo.png" alt="" height="60">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide"
                                                    data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="0" class="active" aria-current="true"
                                                            aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">"Nuestro sistema de asistencia y
                                                                planillas facilita la gestión del personal de manera
                                                                rápida y segura."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"Con CAFED promovemos la
                                                                transparencia y eficiencia en la administración
                                                                educativa."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"Tecnología al servicio de la
                                                                educación: control, organización y resultados en tiempo
                                                                real."</p>
                                                        </div>
                                                    </div>

                                                </div>
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Bienvenido de nuevo !</h5>
                                            <p class="text-muted">Inicie sesión para continuar.</p>
                                        </div>

                                        <div class="mt-4">
                                            <form id="form_login">

                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="username"
                                                        placeholder="Ingrese su usuario" required
                                                        autocomplete="username">
                                                </div>

                                                <div class="mb-3">
                                                    <div class="float-end">
                                                        <a href="#" class="text-muted">¿Olvidó su contraseña?</a>
                                                    </div>
                                                    <label class="form-label" for="password">Password</label>
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5"
                                                            placeholder="Ingrese su contraseña" id="password" required
                                                            autocomplete="current-password">
                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted"
                                                            type="button" id="password-addon"><i
                                                                class="ri-eye-fill align-middle"></i></button>
                                                    </div>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="auth-remember-check">
                                                    <label class="form-check-label"
                                                        for="auth-remember-check">Recordarme</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit" id="btn_login">
                                                        <i class="ri-login-box-line me-1"></i>Iniciar Sesión
                                                    </button>
                                                </div>

                                                <!--<div class="mt-4 text-center">
                                                    <div class="signin-other-title">
                                                        <h5 class="fs-13 mb-4 title">Sign In with</h5>
                                                    </div>

                                                    <div>
                                                        <button type="button"
                                                            class="btn btn-primary btn-icon waves-effect waves-light"><i
                                                                class="ri-facebook-fill fs-16"></i></button>
                                                        <button type="button"
                                                            class="btn btn-danger btn-icon waves-effect waves-light"><i
                                                                class="ri-google-fill fs-16"></i></button>
                                                        <button type="button"
                                                            class="btn btn-dark btn-icon waves-effect waves-light"><i
                                                                class="ri-github-fill fs-16"></i></button>
                                                        <button type="button"
                                                            class="btn btn-info btn-icon waves-effect waves-light"><i
                                                                class="ri-twitter-fill fs-16"></i></button>
                                                    </div>
                                                </div> -->

                                            </form>
                                        </div>

                                        <!-- <div class="mt-5 text-center">
                                            <p class="mb-0">¿No tienes una cuenta?<a href="auth-signup-cover.html"
                                                    class="fw-semibold text-primary text-decoration-underline">
                                                    Signup</a> </p>
                                        </div> -->
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>document.write(new Date().getFullYear())</script> CAFED - Comité de
                                Administración del Fondo Educativo del Callao.
                                </i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery Toast Plugin -->
    <script src="node_modules/jquery-toast-plugin/dist/jquery.toast.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Configuración global de rutas desde PHP -->
    <script>
        // Obtener ruta base desde PHP Enrutamiento
        window.BASE_URL = '<?= Enrutamiento::dominio() ?>';
        window.API_BASE_URL = window.BASE_URL + '/api';
    </script>

    <!-- Auth Helper -->
    <script src="<?= $base_url ?>/helpers/Auth.js"></script>

    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- password-addon init -->
    <script src="assets/js/pages/password-addon.init.js"></script>

    <!-- SweetAlert2 -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/pages/sweetalerts.init.js"></script>

    <!-- Login Script -->
    <script>
        $(document).ready(function () {
            // Si ya está autenticado, redirigir al dashboard
            if (Auth.isAuthenticated()) {
                window.location.href = window.BASE_URL + '/dashboard';
            }

            // Manejar el submit del formulario
            $('#form_login').on('submit', async function (e) {
                e.preventDefault();

                const username = $('#username').val().trim();
                const password = $('#password').val();

                // Validar campos
                if (!username || !password) {
                    $.toast({
                        heading: 'Campos incompletos',
                        text: 'Por favor complete todos los campos',
                        icon: 'warning',
                        position: 'top-right',
                        loader: true,
                        loaderBg: '#f8b739',
                        hideAfter: 3000
                    });
                    return;
                }

                // Mostrar SweetAlert de loading
                Swal.fire({
                    title: 'Iniciando sesión...',
                    html: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    // Hacer login
                    const resultado = await Auth.login(username, password, 1);

                    if (resultado.ok) {
                        // Login exitoso - Redirigir inmediatamente
                        window.location.href = window.BASE_URL + '/dashboard';

                    } else {
                        // Error en login - Mostrar toast de error
                        Swal.close();
                        $.toast({
                            heading: 'Error de autenticación',
                            text: resultado.msj,
                            icon: 'error',
                            position: 'top-right',
                            loader: true,
                            loaderBg: '#bf441d',
                            hideAfter: 4000
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.close();
                    $.toast({
                        heading: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud',
                        icon: 'error',
                        position: 'top-right',
                        loader: true,
                        loaderBg: '#bf441d',
                        hideAfter: 4000
                    });
                }
            });

            // Toggle para mostrar/ocultar contraseña
            $('#password-addon').on('click', function () {
                const passwordInput = $('#password');
                const icon = $(this).find('i');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('ri-eye-fill').addClass('ri-eye-off-fill');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('ri-eye-off-fill').addClass('ri-eye-fill');
                }
            });
        });
    </script>
</body>

</html>