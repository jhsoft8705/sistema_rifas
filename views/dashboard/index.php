<!doctype html>
<html lang="en" data-layout="horizontal" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">
<?php
require_once __DIR__ . "/../../config/Enrutamiento.php";
?>
<head>

    <meta charset="utf-8" />
    <title>Dashboard | Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Rifas" name="Gestión de rifas" />
    <meta content="Rifas" name="Team Rifas" />
    <?php require_once __DIR__. '/../components/head.php' ?>

</head>

<body>

    <div id="layout-wrapper">

        <?php require_once __DIR__.'/../components/navbar.php' ?>
        <?php require_once __DIR__.'/../components/appmenu.php' ?>
        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Starter</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="<?= Enrutamiento::dominio()?>/dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div> 
        <?php require_once __DIR__.'/../components/footer.php' ?>
        </div>

    </div>
    <?php // require_once('../components/themesettings.php') ?>
    <?php require_once __DIR__.'/../components/js.php'?> 

    <!-- Dashboard Init -->
    <script>
        // Proteger ruta - Requiere autenticación
        if (!Auth.isAuthenticated()) {
            window.location.href = window.BASE_URL;
        }

        // Manejar errores de elementos null del app.js
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes("Cannot read properties of null")) {
                console.warn('Elemento del DOM no encontrado - ignorando:', e.message);
                e.preventDefault();
                return true;
            }
        }, true);

        console.log('✓ Dashboard cargado correctamente');
    </script>

</body>

</html>
