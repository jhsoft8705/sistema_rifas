<?php
/**
 * TÉRMINOS Y CONDICIONES - Versión Producción
 * 
 * Usa Enrutamiento::dominio() para rutas absolutas.
 * Compatible con subdominio y dominio.
 * 
 * INSTRUCCIONES: Copiar a views/web/terminos/index.php
 */
require_once __DIR__ . '/../../../config/Enrutamiento.php';
$base_url = Enrutamiento::dominio();
?>
<!doctype html>
<html lang="es" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">

<head>
    <meta charset="utf-8" />
    <title>Términos y Condiciones - Sistema de Rifas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Términos y condiciones del sistema de rifas" name="description" />
    <meta content="Sistema de Rifas" name="author" />

    <link rel="shortcut icon" href="<?= $base_url ?>/assets/images/favicon.ico">
    <script src="<?= $base_url ?>/assets/js/layout.js"></script>
    <link href="<?= $base_url ?>/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base_url ?>/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base_url ?>/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $base_url ?>/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <div class="layout-wrapper landing">
        <section class="section pt-5 mt-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card border shadow-sm">
                            <div class="card-body p-5">
                                <div class="text-center mb-5">
                                    <h1 class="fw-bold mb-3"><i class="ri-file-text-line text-primary"></i> Términos y Condiciones</h1>
                                    <p class="text-muted">Sistema de Rifas - Última actualización: <?php echo date('d/m/Y'); ?></p>
                                </div>

                                <div class="terms-content">
                                    <h4 class="mb-3">1. Aceptación de los Términos</h4>
                                    <p class="text-muted mb-4">
                                        Al participar en cualquiera de nuestras rifas, usted acepta estar legalmente vinculado por estos términos y condiciones.
                                        Si no está de acuerdo con alguna parte de estos términos, no debe participar en nuestras rifas.
                                    </p>

                                    <h4 class="mb-3">2. Elegibilidad para Participar</h4>
                                    <ul class="text-muted mb-4">
                                        <li>Deben ser mayores de 18 años de edad</li>
                                        <li>Tener capacidad legal para celebrar contratos</li>
                                        <li>Proporcionar información veraz y actualizada</li>
                                        <li>Residir en el país donde se realiza la rifa</li>
                                    </ul>

                                    <h4 class="mb-3">3. Compra de Tickets</h4>
                                    <p class="text-muted mb-2"><strong>3.1 Proceso de Compra:</strong></p>
                                    <ul class="text-muted mb-3">
                                        <li>Los tickets se venden por orden de llegada hasta agotar existencias</li>
                                        <li>La compra se considera válida solo después de la validación del pago</li>
                                        <li>Los números de ticket se asignan automáticamente después de la validación</li>
                                        <li>No se aceptan devoluciones una vez validado el pago</li>
                                    </ul>

                                    <p class="text-muted mb-2"><strong>3.2 Métodos de Pago:</strong></p>
                                    <ul class="text-muted mb-4">
                                        <li>Transferencia bancaria (Interbank, BCP)</li>
                                        <li>Billeteras digitales (Yape, Plin)</li>
                                        <li>El pago debe realizarse dentro de las 24 horas posteriores a la solicitud</li>
                                        <li>Debe enviar el comprobante de pago para validación</li>
                                    </ul>

                                    <h4 class="mb-3">4. Validación de Pagos</h4>
                                    <ul class="text-muted mb-4">
                                        <li>Los pagos son validados en un plazo máximo de 24 horas hábiles</li>
                                        <li>Los comprobantes deben ser legibles y contener la información completa</li>
                                        <li>Nos reservamos el derecho de rechazar comprobantes ilegibles o fraudulentos</li>
                                        <li>Una vez validado el pago, recibirá sus números de ticket por correo electrónico</li>
                                    </ul>

                                    <h4 class="mb-3">5. Asignación de Números</h4>
                                    <ul class="text-muted mb-4">
                                        <li>Los números de ticket son asignados automáticamente por el sistema</li>
                                        <li>Cada número es único y no puede ser duplicado</li>
                                        <li>Los números asignados son definitivos y no pueden ser cambiados</li>
                                        <li>El participante recibirá la confirmación de sus números por correo electrónico</li>
                                    </ul>

                                    <h4 class="mb-3">6. Sorteos</h4>
                                    <p class="text-muted mb-2"><strong>6.1 Realización del Sorteo:</strong></p>
                                    <ul class="text-muted mb-3">
                                        <li>Los sorteos se realizan en la fecha y hora especificadas para cada rifa</li>
                                        <li>El sorteo es público y puede ser transmitido en vivo</li>
                                        <li>Utilizamos un sistema aleatorio certificado para garantizar la transparencia</li>
                                        <li>Todos los sorteos son grabados y archivados</li>
                                    </ul>

                                    <p class="text-muted mb-2"><strong>6.2 Selección de Ganadores:</strong></p>
                                    <ul class="text-muted mb-4">
                                        <li>Se seleccionan ganadores según la configuración de cada rifa (1° premio, 2° premio, etc.)</li>
                                        <li>El sistema registra hash de verificación para comprobar legitimidad</li>
                                        <li>Los ganadores son notificados por correo electrónico y teléfono</li>
                                        <li>Los resultados son publicados en nuestro sitio web</li>
                                    </ul>

                                    <h4 class="mb-3">7. Entrega de Premios</h4>
                                    <ul class="text-muted mb-4">
                                        <li>Los ganadores deben presentar identificación oficial válida</li>
                                        <li>Deben proporcionar el código de ticket ganador</li>
                                        <li>Los premios deben ser reclamados dentro de los 30 días posteriores al sorteo</li>
                                        <li>Los premios no reclamados quedan en posesión de la organización</li>
                                        <li>La entrega se coordina directamente con el ganador</li>
                                        <li>Todos los premios son entregados con documentación oficial</li>
                                    </ul>

                                    <h4 class="mb-3">8. Responsabilidades</h4>
                                    <p class="text-muted mb-2"><strong>8.1 Del Participante:</strong></p>
                                    <ul class="text-muted mb-3">
                                        <li>Proporcionar información veraz y actualizada</li>
                                        <li>Conservar su código de ticket y comprobante de pago</li>
                                        <li>Responder a las notificaciones en caso de resultar ganador</li>
                                        <li>Cumplir con los requisitos para reclamar el premio</li>
                                    </ul>

                                    <p class="text-muted mb-2"><strong>8.2 De la Organización:</strong></p>
                                    <ul class="text-muted mb-4">
                                        <li>Garantizar la transparencia en todos los sorteos</li>
                                        <li>Entregar los premios según lo ofrecido</li>
                                        <li>Validar los pagos en el tiempo establecido</li>
                                        <li>Notificar a los ganadores oportunamente</li>
                                    </ul>

                                    <h4 class="mb-3">9. Cancelaciones y Reembolsos</h4>
                                    <ul class="text-muted mb-4">
                                        <li>No se realizan reembolsos una vez validado el pago</li>
                                        <li>Si una rifa es cancelada, se reembolsará el 100% del monto pagado</li>
                                        <li>Nos reservamos el derecho de cancelar una rifa por causas de fuerza mayor</li>
                                        <li>Los reembolsos se procesan en un plazo de 15 días hábiles</li>
                                    </ul>

                                    <h4 class="mb-3">10. Privacidad y Protección de Datos</h4>
                                    <ul class="text-muted mb-4">
                                        <li>Sus datos personales son tratados conforme a nuestra Política de Privacidad</li>
                                        <li>No compartimos su información con terceros sin su consentimiento</li>
                                        <li>Los datos son utilizados únicamente para fines de la rifa</li>
                                        <li>Puede solicitar la eliminación de sus datos en cualquier momento</li>
                                    </ul>

                                    <h4 class="mb-3">11. Limitación de Responsabilidad</h4>
                                    <ul class="text-muted mb-4">
                                        <li>No nos hacemos responsables por errores del usuario al proporcionar información</li>
                                        <li>No somos responsables por problemas técnicos ajenos a nuestro control</li>
                                        <li>Los premios se entregan "tal como están" según la descripción publicada</li>
                                        <li>Cualquier impuesto derivado del premio es responsabilidad del ganador</li>
                                    </ul>

                                    <h4 class="mb-3">12. Modificaciones</h4>
                                    <p class="text-muted mb-4">
                                        Nos reservamos el derecho de modificar estos términos y condiciones en cualquier momento.
                                        Los cambios entrarán en vigor inmediatamente después de su publicación en el sitio web.
                                        Es responsabilidad del usuario revisar periódicamente estos términos.
                                    </p>

                                    <h4 class="mb-3">13. Ley Aplicable y Jurisdicción</h4>
                                    <p class="text-muted mb-4">
                                        Estos términos se rigen por las leyes del país donde se realiza la rifa.
                                        Cualquier disputa será resuelta en los tribunales competentes de dicha jurisdicción.
                                    </p>

                                    <h4 class="mb-3">14. Contacto</h4>
                                    <p class="text-muted mb-4">
                                        Para cualquier consulta sobre estos términos y condiciones, puede contactarnos a través de:
                                    </p>
                                    <ul class="text-muted mb-4">
                                        <li><strong>Correo:</strong> info@sistemrifas.com</li>
                                        <li><strong>WhatsApp:</strong> +52 1 55 1234 5678</li>
                                        <li><strong>Horario:</strong> Lunes a Domingo 9:00am a 9:00pm</li>
                                    </ul>

                                    <div class="alert alert-info border-0 mt-5">
                                        <h6 class="alert-heading"><i class="ri-information-line me-2"></i>Aceptación</h6>
                                        <p class="mb-0">
                                            Al marcar la casilla "Acepto los términos y condiciones" en el formulario de compra,
                                            usted confirma que ha leído, entendido y acepta cumplir con todos estos términos y condiciones.
                                        </p>
                                    </div>

                                    <div class="text-center mt-5">
                                        <a href="<?= $base_url ?>" class="btn btn-primary btn-lg">
                                            <i class="ri-arrow-left-line me-1"></i> Volver al Inicio
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="custom-footer bg-dark py-4 position-relative mt-5">
            <div class="container">
                <div class="row text-center">
                    <div class="col-12">
                        <p class="copy-rights mb-0 text-white-50">
                            <script> document.write(new Date().getFullYear()) </script> © Sistema de Rifas - Todos los derechos reservados
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="<?= $base_url ?>/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base_url ?>/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= $base_url ?>/assets/libs/node-waves/waves.min.js"></script>
    <script src="<?= $base_url ?>/assets/libs/feather-icons/feather.min.js"></script>
    <script src="<?= $base_url ?>/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="<?= $base_url ?>/assets/js/plugins.js"></script>
</body>

</html>
