<?php
require_once __DIR__ . "/../../config/Enrutamiento.php";
?>
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a href="<?= Enrutamiento::dominio()?>/dashboard" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?= Enrutamiento::dominio()?>/assets/images/logo-mini.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= Enrutamiento::dominio()?>/assets/images/logo.png" alt="" height="58">
                        </span>
                    </a>

                    <a href="<?= Enrutamiento::dominio()?>/dashboard" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?= Enrutamiento::dominio()?>/assets/images/logo-mini.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="<?= Enrutamiento::dominio()?>/assets/images/logo.png" alt="" height="58">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Buscar..." autocomplete="off"
                            id="search-options" value="">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none"
                            id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Resultados de búsqueda</h6>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <!-- Botón fullscreen -->
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                        data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <!-- Botón dark mode -->
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>

                <!-- Dropdown de usuario -->
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="<?= Enrutamiento::dominio()?>/assets/images/users/avatar-1.jpg"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text" id="navbar_nombre_usuario">Usuario</span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text" id="navbar_rol_usuario">Rol</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- Nombre del usuario -->
                        <h6 class="dropdown-header">
                            Bienvenido <span id="navbar_nombre_completo">Usuario</span>!
                        </h6>
                        
                        <!-- Información adicional -->
                        <div class="dropdown-item">
                            <small class="text-muted">
                                <i class="mdi mdi-office-building me-1"></i>
                                <span id="navbar_sede_nombre">Sede</span>
                            </small>
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        <!-- Perfil -->
                        <a class="dropdown-item" href="<?= Enrutamiento::dominio()?>/views/perfil">
                            <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> 
                            <span class="align-middle">Mi Perfil</span>
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <!-- Logout -->
                        <a class="dropdown-item" href="#" id="btn_logout">
                            <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> 
                            <span class="align-middle" data-key="t-logout">Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Variables ocultas del usuario autenticado (accesibles desde JavaScript) -->
<input type="hidden" id="usuario_autenticado_id" value="">
<input type="hidden" id="usuario_autenticado_nombre" value="">
<input type="hidden" id="usuario_autenticado_sede_id" value="">
<input type="hidden" id="usuario_autenticado_sede_nombre" value="">
<input type="hidden" id="usuario_autenticado_rol_id" value="">
<input type="hidden" id="usuario_autenticado_rol_nombre" value="">

<!-- Script para cargar información del usuario en el navbar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que Auth esté disponible
    if (typeof Auth !== 'undefined') {
        const userInfo = Auth.getUserInfo();
        
        if (userInfo) {
            // Actualizar navbar
            document.getElementById('navbar_nombre_usuario').textContent = userInfo.nombre_completo;
            document.getElementById('navbar_rol_usuario').textContent = userInfo.rol_nombre;
            document.getElementById('navbar_nombre_completo').textContent = userInfo.nombre_completo;
            document.getElementById('navbar_sede_nombre').textContent = userInfo.sede_nombre;
            
            // Actualizar inputs ocultos
            document.getElementById('usuario_autenticado_id').value = userInfo.usuario_id;
            document.getElementById('usuario_autenticado_nombre').value = userInfo.nombre_completo;
            document.getElementById('usuario_autenticado_sede_id').value = userInfo.sede_id;
            document.getElementById('usuario_autenticado_sede_nombre').value = userInfo.sede_nombre;
            document.getElementById('usuario_autenticado_rol_id').value = userInfo.rol_id;
            document.getElementById('usuario_autenticado_rol_nombre').value = userInfo.rol_nombre;
            
/*             console.log(' Usuario cargado en navbar:', userInfo.nombre_completo);
 */        }
    }
    
    // Manejar logout
    document.getElementById('btn_logout')?.addEventListener('click', async function(e) {
        e.preventDefault();
        
        const result = await Swal.fire({
            title: '¿Cerrar sesión?',
            text: '¿Está seguro que desea salir del sistema?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, salir',
            cancelButtonText: 'Cancelar'
        });
        
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Cerrando sesión...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            await Auth.logout();
        }
    });
});
</script>
