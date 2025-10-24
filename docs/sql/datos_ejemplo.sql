-- =====================================================
-- DATOS DE EJEMPLO - SISTEMA DE RIFAS
-- Datos de prueba para desarrollo y testing
-- =====================================================

USE sistema_rifas;

-- =====================================================
-- 1. UBICACIONES DE EJEMPLO
-- =====================================================

-- Ubicaciones Perú
INSERT INTO ubicaciones_rifa (sede_id, nombre, direccion, ciudad, departamento_region, pais, telefono, latitud, longitud, es_principal, creado_por) VALUES
(1, 'Oficina Lima Centro', 'Av. Javier Prado Este 123, San Isidro', 'Lima', 'Lima', 'Perú', '999888777', -12.0464, -77.0428, 1, 'SYSTEM'),
(1, 'Sucursal Miraflores', 'Av. Larco 456, Miraflores', 'Lima', 'Lima', 'Perú', '999888778', -12.1196, -77.0282, 0, 'SYSTEM'),
(1, 'Sucursal Chiclayo', 'Av. Balta 789, Chiclayo', 'Chiclayo', 'Lambayeque', 'Perú', '999888779', -6.7711, -79.8369, 0, 'SYSTEM');

-- Ubicaciones Colombia
INSERT INTO ubicaciones_rifa (sede_id, nombre, direccion, ciudad, departamento_region, pais, telefono, latitud, longitud, es_principal, creado_por) VALUES
(2, 'Oficina Bogotá Centro', 'Calle 100 #15-20', 'Bogotá', 'Cundinamarca', 'Colombia', '3001234567', 4.6867, -74.0548, 1, 'SYSTEM');

-- =====================================================
-- 2. CATEGORÍAS DE PREMIOS
-- =====================================================

INSERT INTO categorias_premios (sede_id, nombre, descripcion, icono, color_hex, orden, creado_por) VALUES
(1, 'Electrónica', 'Smartphones, laptops, tablets y más', 'las la-mobile', '#2196F3', 1, 'SYSTEM'),
(1, 'Vehículos', 'Autos, motos y bicicletas', 'las la-car', '#FF5722', 2, 'SYSTEM'),
(1, 'Viajes', 'Paquetes turísticos nacionales e internacionales', 'las la-plane', '#4CAF50', 3, 'SYSTEM'),
(1, 'Dinero en Efectivo', 'Premios en efectivo', 'las la-money-bill-wave', '#FFC107', 4, 'SYSTEM'),
(1, 'Electrodomésticos', 'Refrigeradoras, cocinas, lavadoras', 'las la-blender', '#9C27B0', 5, 'SYSTEM');

-- Categorías para otras sedes
INSERT INTO categorias_premios (sede_id, nombre, descripcion, icono, color_hex, orden, creado_por)
SELECT id, 'Electrónica', 'Smartphones, laptops, tablets y más', 'las la-mobile', '#2196F3', 1, 'SYSTEM' FROM sedes WHERE id > 1
UNION ALL
SELECT id, 'Vehículos', 'Autos, motos y bicicletas', 'las la-car', '#FF5722', 2, 'SYSTEM' FROM sedes WHERE id > 1
UNION ALL
SELECT id, 'Dinero en Efectivo', 'Premios en efectivo', 'las la-money-bill-wave', '#FFC107', 3, 'SYSTEM' FROM sedes WHERE id > 1;

-- =====================================================
-- 3. PREMIOS DE EJEMPLO
-- =====================================================

-- Premios Perú
INSERT INTO premios (sede_id, categoria_id, codigo, nombre, descripcion, valor_estimado, marca, modelo, especificaciones, imagen_principal, es_destacado, orden_visualizacion, creado_por) VALUES
(1, 1, 'IPHONE15-001', 'iPhone 15 Pro Max', 
 '<p>El último modelo de Apple con cámara de 48MP, chip A17 Pro y pantalla Super Retina XDR de 6.7"</p>', 
 5499.00, 'Apple', 'iPhone 15 Pro Max', 
 '{"capacidad":"256GB","color":"Titanio Natural","garantia":"1 año Apple Care"}',
 '/assets/premios/iphone15.jpg', 1, 1, 'SYSTEM'),

(1, 1, 'LAPTOP-001', 'MacBook Air M2', 
 '<p>Laptop ultradelgada con chip M2, perfecta para trabajo y creatividad</p>', 
 4999.00, 'Apple', 'MacBook Air M2', 
 '{"memoria":"16GB","almacenamiento":"512GB SSD","pantalla":"13.6 pulgadas"}',
 '/assets/premios/macbook.jpg', 1, 2, 'SYSTEM'),

(1, 2, 'MOTO-001', 'Yamaha MT-03', 
 '<p>Moto deportiva 321cc, perfecta para ciudad y carretera</p>', 
 18500.00, 'Yamaha', 'MT-03', 
 '{"cilindraje":"321cc","año":"2024","color":"Azul Racing"}',
 '/assets/premios/yamaha-mt03.jpg', 1, 3, 'SYSTEM'),

(1, 3, 'VIAJE-001', 'Viaje a Cusco All Inclusive', 
 '<p>4 días y 3 noches en Cusco, incluye tours, hotel 4 estrellas y alimentación</p>', 
 3500.00, 'Tours Peru', 'Paquete Premium', 
 '{"duracion":"4 dias","personas":"2","incluye":"Hotel + Tours + Alimentacion"}',
 '/assets/premios/cusco.jpg', 1, 4, 'SYSTEM'),

(1, 4, 'EFECTIVO-001', 'S/. 10,000 en Efectivo', 
 '<p>Diez mil soles en efectivo para que los uses en lo que quieras</p>', 
 10000.00, NULL, NULL, 
 '{"moneda":"PEN","monto":"10000"}',
 '/assets/premios/dinero.jpg', 1, 5, 'SYSTEM'),

(1, 5, 'ELECTRO-001', 'Refrigeradora Samsung Side by Side', 
 '<p>Refrigeradora de última generación con tecnología No Frost</p>', 
 4200.00, 'Samsung', 'RS27T5200S9', 
 '{"capacidad":"700 litros","color":"Inoxidable","tecnologia":"No Frost"}',
 '/assets/premios/refrigeradora.jpg', 0, 6, 'SYSTEM');

-- =====================================================
-- 4. RIFAS DE EJEMPLO
-- =====================================================

-- Rifa 1: iPhone 15 Pro Max (EN_VENTA)
INSERT INTO rifas (
    sede_id, premio_id, ubicacion_id, codigo, nombre, descripcion,
    numero_intentos, intento_ganador, precio_ticket, cantidad_maxima_tickets,
    cantidad_maxima_por_persona, fecha_inicio_venta, fecha_fin_venta, fecha_sorteo,
    mostrar_contador, mostrar_participantes, mostrar_tickets_vendidos,
    tipo_publicidad, texto_promocional, estado, estado_activo, creado_por
) VALUES (
    1, 1, 1, 'RIFA-2025-001', 'Gana un iPhone 15 Pro Max', 
    '<p>¡Gran oportunidad de ganar el último iPhone 15 Pro Max! Solo S/. 10 por ticket. Sorteo en vivo por Facebook.</p>',
    5, 5, 10.00, 500, 3,
    '2025-01-20 00:00:00', '2025-02-15 23:59:59', '2025-02-16 19:00:00',
    1, 1, 1, 'Banner Principal', 
    '🎉 ¡SORTEO DEL SIGLO! Gana un iPhone 15 Pro Max por solo S/. 10 🎉 #Rifa #iPhone15',
    'EN_VENTA', 1, 'admin'
);

-- Rifa 2: MacBook Air (PUBLICADA - aún no en venta)
INSERT INTO rifas (
    sede_id, premio_id, ubicacion_id, codigo, nombre, descripcion,
    numero_intentos, intento_ganador, precio_ticket, cantidad_maxima_tickets,
    cantidad_maxima_por_persona, fecha_inicio_venta, fecha_fin_venta, fecha_sorteo,
    mostrar_contador, mostrar_participantes, mostrar_tickets_vendidos,
    estado, estado_activo, creado_por
) VALUES (
    1, 2, 1, 'RIFA-2025-002', 'Gana una MacBook Air M2', 
    '<p>Laptop de última generación para trabajo y estudio. ¡No te lo pierdas!</p>',
    5, 5, 15.00, 400, 2,
    '2025-02-17 00:00:00', '2025-03-17 23:59:59', '2025-03-18 20:00:00',
    1, 1, 1, 'PUBLICADA', 1, 'admin'
);

-- Rifa 3: Yamaha MT-03 (BORRADOR)
INSERT INTO rifas (
    sede_id, premio_id, ubicacion_id, codigo, nombre, descripcion,
    numero_intentos, intento_ganador, precio_ticket, cantidad_maxima_tickets,
    cantidad_maxima_por_persona, fecha_inicio_venta, fecha_fin_venta, fecha_sorteo,
    estado, estado_activo, creado_por
) VALUES (
    1, 3, 1, 'RIFA-2025-003', 'Gana una Moto Yamaha MT-03', 
    '<p>¡La moto de tus sueños puede ser tuya! Sorteo especial.</p>',
    7, 7, 20.00, 1000, 5,
    '2025-03-01 00:00:00', '2025-04-30 23:59:59', '2025-05-01 19:00:00',
    'BORRADOR', 1, 'admin'
);

-- =====================================================
-- 5. TICKETS DE EJEMPLO (Solo para testing)
-- =====================================================

-- Tickets para la rifa del iPhone (RIFA-2025-001)
INSERT INTO tickets (
    sede_id, rifa_id, codigo_ticket, nombres, apellidos,
    tipo_documento, numero_documento, email, telefono,
    ciudad, pais, precio_pagado, estado, fecha_validez
) VALUES
-- Tickets APROBADOS (ya son participantes)
(1, 1, 'PERU-20250120-000001', 'María', 'García López', 'DNI', '12345678', 'maria.garcia@email.com', '999111222', 'Lima', 'Perú', 10.00, 'APROBADO', DATE_ADD(NOW(), INTERVAL 90 DAY)),
(1, 1, 'PERU-20250120-000002', 'Juan', 'Pérez Rodríguez', 'DNI', '23456789', 'juan.perez@email.com', '999222333', 'Lima', 'Perú', 10.00, 'APROBADO', DATE_ADD(NOW(), INTERVAL 90 DAY)),
(1, 1, 'PERU-20250121-000003', 'Carlos', 'Mendoza Silva', 'DNI', '34567890', 'carlos.mendoza@email.com', '999333444', 'Chiclayo', 'Perú', 10.00, 'APROBADO', DATE_ADD(NOW(), INTERVAL 90 DAY)),
(1, 1, 'PERU-20250121-000004', 'Ana', 'Torres Vega', 'DNI', '45678901', 'ana.torres@email.com', '999444555', 'Lima', 'Perú', 10.00, 'APROBADO', DATE_ADD(NOW(), INTERVAL 90 DAY)),
(1, 1, 'PERU-20250122-000005', 'Luis', 'Ramírez Castro', 'DNI', '56789012', 'luis.ramirez@email.com', '999555666', 'Arequipa', 'Perú', 10.00, 'APROBADO', DATE_ADD(NOW(), INTERVAL 90 DAY)),

-- Tickets PENDIENTES de validación
(1, 1, 'PERU-20250123-000006', 'Rosa', 'Flores Díaz', 'DNI', '67890123', 'rosa.flores@email.com', '999666777', 'Lima', 'Perú', 10.00, 'PAGO_SUBIDO', DATE_ADD(NOW(), INTERVAL 90 DAY)),
(1, 1, 'PERU-20250123-000007', 'Pedro', 'Sánchez Ruiz', 'DNI', '78901234', 'pedro.sanchez@email.com', '999777888', 'Lima', 'Perú', 10.00, 'PAGO_SUBIDO', DATE_ADD(NOW(), INTERVAL 90 DAY)),

-- Ticket PENDIENTE de pago
(1, 1, 'PERU-20250124-000008', 'Laura', 'Martínez Huamán', 'DNI', '89012345', 'laura.martinez@email.com', '999888999', 'Lima', 'Perú', 10.00, 'PENDIENTE_PAGO', DATE_ADD(NOW(), INTERVAL 90 DAY));

-- =====================================================
-- 6. ACTUALIZAR APROBACIONES EN TICKETS
-- =====================================================

-- Marcar tickets aprobados con información de aprobación
UPDATE tickets SET 
    aprobado_por = 'admin',
    fecha_aprobacion = DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 5) DAY)
WHERE estado = 'APROBADO';

-- =====================================================
-- 7. COMPROBANTES DE PAGO
-- =====================================================

-- Comprobantes para tickets aprobados
INSERT INTO comprobantes_pago (
    sede_id, ticket_id, metodo_pago_id, numero_operacion, 
    monto, fecha_pago, archivo_comprobante, tipo_archivo,
    estado, validado_por, fecha_validacion
)
SELECT 
    t.sede_id, 
    t.id,
    1, -- Yape
    CONCAT('OP', LPAD(t.id, 8, '0')),
    t.precio_pagado,
    DATE_SUB(t.fecha_aprobacion, INTERVAL 1 DAY),
    CONCAT('/assets/comprobantes/', t.codigo_ticket, '.jpg'),
    'jpg',
    'APROBADO',
    'admin',
    t.fecha_aprobacion
FROM tickets t
WHERE t.estado = 'APROBADO';

-- Comprobantes para tickets pendientes
INSERT INTO comprobantes_pago (
    sede_id, ticket_id, metodo_pago_id, numero_operacion, 
    monto, fecha_pago, archivo_comprobante, tipo_archivo,
    estado
)
SELECT 
    t.sede_id, 
    t.id,
    1, -- Yape
    CONCAT('OP', LPAD(t.id, 8, '0')),
    t.precio_pagado,
    DATE_SUB(NOW(), INTERVAL 1 DAY),
    CONCAT('/assets/comprobantes/', t.codigo_ticket, '.jpg'),
    'jpg',
    'PENDIENTE'
FROM tickets t
WHERE t.estado = 'PAGO_SUBIDO';

-- =====================================================
-- 8. PARTICIPANTES (Solo tickets aprobados)
-- =====================================================

INSERT INTO participantes (sede_id, rifa_id, ticket_id, numero_participacion)
SELECT 
    t.sede_id,
    t.rifa_id,
    t.id,
    ROW_NUMBER() OVER (PARTITION BY t.rifa_id ORDER BY t.fecha_aprobacion)
FROM tickets t
WHERE t.estado = 'APROBADO';

-- =====================================================
-- 9. ACTUALIZAR CONTADOR DE TICKETS VENDIDOS
-- =====================================================

UPDATE rifas r
SET tickets_vendidos = (
    SELECT COUNT(*) 
    FROM tickets t 
    WHERE t.rifa_id = r.id 
    AND t.estado = 'APROBADO'
);

-- =====================================================
-- 10. CONFIGURACIONES ADICIONALES
-- =====================================================

-- Configuraciones de landing page para Perú
INSERT INTO configuracion_sede (sede_id, clave, valor, descripcion, tipo_dato, creado_por) VALUES
(1, 'landing_titulo', 'Gana Increíbles Premios', 'Título principal de la landing', 'STRING', 'SYSTEM'),
(1, 'landing_subtitulo', 'Participa en nuestros sorteos y cambia tu vida', 'Subtítulo de la landing', 'STRING', 'SYSTEM'),
(1, 'landing_color_primario', '#FF5722', 'Color primario de la landing', 'STRING', 'SYSTEM'),
(1, 'landing_color_secundario', '#2196F3', 'Color secundario de la landing', 'STRING', 'SYSTEM'),
(1, 'landing_footer_texto', '© 2025 Sistema de Rifas. Todos los derechos reservados.', 'Texto del footer', 'STRING', 'SYSTEM'),
(1, 'max_intentos_upload', '3', 'Máximo de intentos para subir comprobante', 'INTEGER', 'SYSTEM'),
(1, 'tamano_maximo_archivo_mb', '5', 'Tamaño máximo de archivo en MB', 'INTEGER', 'SYSTEM'),
(1, 'facebook_url', 'https://facebook.com/sistemrifas', 'URL de Facebook', 'STRING', 'SYSTEM'),
(1, 'instagram_url', 'https://instagram.com/sistemarifas', 'URL de Instagram', 'STRING', 'SYSTEM'),
(1, 'whatsapp_soporte', '+51999888777', 'WhatsApp de soporte', 'STRING', 'SYSTEM'),
(1, 'email_soporte', 'soporte@sistemarifas.com', 'Email de soporte', 'STRING', 'SYSTEM');

-- =====================================================
-- FIN DE DATOS DE EJEMPLO
-- =====================================================

-- Resumen de datos insertados
SELECT 'Datos de ejemplo insertados correctamente' AS Resultado;

SELECT 
    'Ubicaciones' AS Tabla, COUNT(*) AS Total FROM ubicaciones_rifa
UNION ALL
SELECT 'Categorías', COUNT(*) FROM categorias_premios
UNION ALL
SELECT 'Premios', COUNT(*) FROM premios
UNION ALL
SELECT 'Rifas', COUNT(*) FROM rifas
UNION ALL
SELECT 'Tickets', COUNT(*) FROM tickets
UNION ALL
SELECT 'Comprobantes', COUNT(*) FROM comprobantes_pago
UNION ALL
SELECT 'Participantes', COUNT(*) FROM participantes
UNION ALL
SELECT 'Configuraciones', COUNT(*) FROM configuracion_sede;

