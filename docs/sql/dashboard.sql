-- =============================================
-- STORED PROCEDURES PARA DASHBOARD
-- Sistema de Gestión de Rifas
-- =============================================

DELIMITER //

-- ==========================================================
-- 1. OBTENER KPIs DE VENTAS Y TICKETS
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_kpis_ventas_tickets //
CREATE PROCEDURE dashboard_kpis_ventas_tickets (
    IN p_sede_id INT
)
BEGIN
    SELECT
        -- Tickets vendidos hoy
        COUNT(CASE WHEN DATE(t.fecha_compra) = CURDATE() THEN 1 END) AS tickets_vendidos_hoy,
        
        -- Ingresos hoy (SUM tickets APROBADOS)
        COALESCE(SUM(CASE WHEN DATE(t.fecha_compra) = CURDATE() AND t.estado = 'APROBADO' THEN t.precio_pagado ELSE 0 END), 0) AS ingresos_hoy,
        
        -- Ingresos del mes
        COALESCE(SUM(CASE WHEN YEAR(t.fecha_compra) = YEAR(CURDATE()) 
                         AND MONTH(t.fecha_compra) = MONTH(CURDATE()) 
                         AND t.estado = 'APROBADO' THEN t.precio_pagado ELSE 0 END), 0) AS ingresos_mes,
        
        -- Ticket promedio (ingreso / tickets)
        CASE 
            WHEN COUNT(CASE WHEN t.estado = 'APROBADO' THEN 1 END) > 0 
            THEN COALESCE(SUM(CASE WHEN t.estado = 'APROBADO' THEN t.precio_pagado ELSE 0 END) / 
                          COUNT(CASE WHEN t.estado = 'APROBADO' THEN 1 END), 0)
            ELSE 0
        END AS ticket_promedio
    FROM tickets t
    WHERE t.sede_id = p_sede_id;
END //

-- ==========================================================
-- 2. OBTENER KPIs DE ESTADO OPERATIVO
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_kpis_estado_operativo //
CREATE PROCEDURE dashboard_kpis_estado_operativo (
    IN p_sede_id INT
)
BEGIN
    SELECT
        -- Tickets pendientes de validación
        COUNT(CASE WHEN t.estado IN ('PAGO_SUBIDO', 'VALIDANDO') THEN 1 END) AS tickets_pendientes_validacion,
        
        -- Pagos rechazados hoy
        COUNT(CASE WHEN DATE(t.fecha_rechazo) = CURDATE() AND t.estado = 'RECHAZADO' THEN 1 END) AS pagos_rechazados_hoy,
        
        -- Tickets por expirar (fecha_validez < 3 días)
        COUNT(CASE WHEN t.fecha_validez IS NOT NULL 
                   AND t.fecha_validez > NOW() 
                   AND t.fecha_validez <= DATE_ADD(NOW(), INTERVAL 3 DAY)
                   AND t.estado IN ('PENDIENTE_PAGO', 'PAGO_SUBIDO', 'VALIDANDO') THEN 1 END) AS tickets_por_expirar,
        
        -- Personas únicas participantes
        COUNT(DISTINCT CASE WHEN t.estado IN ('APROBADO', 'PARTICIPANDO', 'GANADOR') THEN t.persona_id END) AS personas_unicas_participantes
    FROM tickets t
    WHERE t.sede_id = p_sede_id;
END //

-- ==========================================================
-- 3. OBTENER KPIs DE RIFAS
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_kpis_rifas //
CREATE PROCEDURE dashboard_kpis_rifas (
    IN p_sede_id INT
)
BEGIN
    SELECT
        -- Rifas activas
        COUNT(CASE WHEN r.estado IN ('PUBLICADA', 'EN_VENTA') AND r.estado_activo = 1 THEN 1 END) AS rifas_activas,
        
        -- Rifa más vendida (nombre y cantidad)
        (SELECT r2.nombre 
         FROM rifas r2
         INNER JOIN tickets t2 ON t2.rifa_id = r2.id
         WHERE r2.sede_id = p_sede_id 
           AND r2.estado_activo = 1
           AND t2.estado IN ('APROBADO', 'PARTICIPANDO', 'GANADOR')
         GROUP BY r2.id, r2.nombre
         ORDER BY COUNT(t2.id) DESC
         LIMIT 1) AS rifa_mas_vendida,
        
        -- Rifa con menor avance (nombre y porcentaje)
        (SELECT r3.nombre
         FROM rifas r3
         WHERE r3.sede_id = p_sede_id 
           AND r3.estado IN ('PUBLICADA', 'EN_VENTA')
           AND r3.estado_activo = 1
         ORDER BY (r3.tickets_vendidos / NULLIF(r3.cantidad_maxima_tickets, 0)) ASC
         LIMIT 1) AS rifa_menor_avance
    FROM rifas r
    WHERE r.sede_id = p_sede_id;
END //

-- ==========================================================
-- 4. OBTENER VENTAS EN EL TIEMPO (Últimos 30 días)
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_ventas_tiempo //
CREATE PROCEDURE dashboard_ventas_tiempo (
    IN p_sede_id INT,
    IN p_dias INT
)
BEGIN
    SELECT
        DATE(t.fecha_compra) AS fecha,
        COUNT(CASE WHEN t.estado = 'APROBADO' THEN 1 END) AS tickets_aprobados,
        COALESCE(SUM(CASE WHEN t.estado = 'APROBADO' THEN t.precio_pagado ELSE 0 END), 0) AS ingresos
    FROM tickets t
    WHERE t.sede_id = p_sede_id
      AND DATE(t.fecha_compra) >= DATE_SUB(CURDATE(), INTERVAL p_dias DAY)
    GROUP BY DATE(t.fecha_compra)
    ORDER BY fecha ASC;
END //

-- ==========================================================
-- 5. OBTENER ESTADO DE TICKETS (Para gráfico Donut)
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_estado_tickets //
CREATE PROCEDURE dashboard_estado_tickets (
    IN p_sede_id INT
)
BEGIN
    SELECT
        t.estado,
        COUNT(*) AS cantidad
    FROM tickets t
    WHERE t.sede_id = p_sede_id
      AND t.estado IN ('PENDIENTE_PAGO', 'PAGO_SUBIDO', 'APROBADO', 'RECHAZADO', 'EXPIRADO')
    GROUP BY t.estado
    ORDER BY cantidad DESC;
END //

-- ==========================================================
-- 6. OBTENER AVANCE DE RIFAS (Para gráfico Bar)
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_avance_rifas //
CREATE PROCEDURE dashboard_avance_rifas (
    IN p_sede_id INT
)
BEGIN
    SELECT
        r.id,
        r.nombre,
        r.tickets_vendidos AS vendidos,
        r.cantidad_maxima_tickets AS total_disponible,
        CASE 
            WHEN r.cantidad_maxima_tickets > 0 
            THEN ROUND((r.tickets_vendidos / r.cantidad_maxima_tickets) * 100, 2)
            ELSE 0
        END AS porcentaje_avance
    FROM rifas r
    WHERE r.sede_id = p_sede_id
      AND r.estado IN ('PUBLICADA', 'EN_VENTA')
      AND r.estado_activo = 1
    ORDER BY porcentaje_avance DESC
    LIMIT 10;
END //

-- ==========================================================
-- 7. OBTENER CANALES DE VENTA (Para gráfico Donut)
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_canales_venta //
CREATE PROCEDURE dashboard_canales_venta (
    IN p_sede_id INT
)
BEGIN
    SELECT
        COALESCE(t.canal_venta, 'WEB') AS canal,
        COUNT(*) AS cantidad
    FROM tickets t
    WHERE t.sede_id = p_sede_id
      AND t.estado IN ('APROBADO', 'PARTICIPANDO', 'GANADOR')
    GROUP BY COALESCE(t.canal_venta, 'WEB')
    ORDER BY cantidad DESC;
END //

-- ==========================================================
-- 8. OBTENER ÚLTIMOS MOVIMIENTOS
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_ultimos_movimientos //
CREATE PROCEDURE dashboard_ultimos_movimientos (
    IN p_sede_id INT
)
BEGIN
    SELECT * FROM (
        -- Últimos 10 tickets comprados
        SELECT 
            'TICKET_COMPRADO' AS tipo,
            t.id,
            t.codigo_ticket,
            CONCAT(p.nombres, ' ', p.apellidos) AS persona_nombre,
            r.nombre AS rifa_nombre,
            t.precio_pagado,
            t.estado,
            t.fecha_compra AS fecha,
            NULL AS motivo_rechazo
        FROM tickets t
        INNER JOIN personas p ON t.persona_id = p.id
        INNER JOIN rifas r ON t.rifa_id = r.id
        WHERE t.sede_id = p_sede_id
        
        UNION ALL
        
        -- Últimos 10 comprobantes subidos
        SELECT 
            'COMPROBANTE_SUBIDO' AS tipo,
            cp.id,
            cp.numero_operacion AS codigo_ticket,
            CONCAT(p.nombres, ' ', p.apellidos) AS persona_nombre,
            r.nombre AS rifa_nombre,
            cp.monto AS precio_pagado,
            cp.estado,
            cp.fecha_creacion AS fecha,
            NULL AS motivo_rechazo
        FROM comprobantes_pago cp
        INNER JOIN tickets t ON cp.ticket_id = t.id
        INNER JOIN personas p ON t.persona_id = p.id
        INNER JOIN rifas r ON t.rifa_id = r.id
        WHERE cp.sede_id = p_sede_id
        
        UNION ALL
        
        -- Últimos 5 pagos rechazados
        SELECT 
            'PAGO_RECHAZADO' AS tipo,
            t.id,
            t.codigo_ticket,
            CONCAT(p.nombres, ' ', p.apellidos) AS persona_nombre,
            r.nombre AS rifa_nombre,
            t.precio_pagado,
            t.estado,
            t.fecha_rechazo AS fecha,
            t.motivo_rechazo
        FROM tickets t
        INNER JOIN personas p ON t.persona_id = p.id
        INNER JOIN rifas r ON t.rifa_id = r.id
        WHERE t.sede_id = p_sede_id
          AND t.estado = 'RECHAZADO'
    ) AS movimientos
    ORDER BY fecha DESC
    LIMIT 25;
END //

-- ==========================================================
-- 9. OBTENER ÚLTIMOS GANADORES
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_ultimos_ganadores //
CREATE PROCEDURE dashboard_ultimos_ganadores (
    IN p_sede_id INT,
    IN p_limite INT
)
BEGIN
    SELECT
        r.nombre AS rifa_nombre,
        pr.nombre AS premio_nombre,
        g.nombre_completo AS persona_nombre,
        COALESCE(nr.numero_formateado, CONCAT('Ticket #', t.id)) AS numero_ganador,
        g.fecha_ganador AS fecha
    FROM ganadores g
    INNER JOIN rifas r ON g.rifa_id = r.id
    INNER JOIN premios pr ON g.premio_id = pr.id
    LEFT JOIN tickets t ON g.ticket_id = t.id
    LEFT JOIN numeros_rifa nr ON g.numero_id = nr.id
    WHERE g.sede_id = p_sede_id
    ORDER BY g.fecha_ganador DESC
    LIMIT p_limite;
END //

-- ==========================================================
-- 10. OBTENER TICKETS APROBADOS (Para tabla)
-- ==========================================================
DROP PROCEDURE IF EXISTS dashboard_tickets_aprobados //
CREATE PROCEDURE dashboard_tickets_aprobados (
    IN p_sede_id INT,
    IN p_limite INT
)
BEGIN
    SELECT
        t.id,
        t.codigo_ticket,
        CONCAT(p.nombres, ' ', p.apellidos) AS persona_nombre,
        r.nombre AS rifa_nombre,
        t.precio_pagado,
        t.fecha_compra,
        t.canal_venta
    FROM tickets t
    INNER JOIN personas p ON t.persona_id = p.id
    INNER JOIN rifas r ON t.rifa_id = r.id
    WHERE t.sede_id = p_sede_id
      AND t.estado = 'APROBADO'
    ORDER BY t.fecha_compra DESC
    LIMIT p_limite;
END //

DELIMITER ;
