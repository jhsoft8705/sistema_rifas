-- =============================================
-- PROCEDIMIENTOS PARA REPORTES
-- Reporte de recaudación por rifa y ganadores
-- =============================================

DELIMITER //

-- 1. REPORTE RECAUDACIÓN POR RIFA (dinero ingresado en un rango de fechas)
-- Tickets en estado APROBADO, PARTICIPANDO o GANADOR
DROP PROCEDURE IF EXISTS reporte_recaudacion_rifa //
CREATE PROCEDURE reporte_recaudacion_rifa (
    IN p_sede_id INT,
    IN p_rifa_id INT,
    IN p_fecha_desde DATE,
    IN p_fecha_hasta DATE
)
BEGIN
    SELECT
        r.id AS rifa_id,
        r.codigo AS rifa_codigo,
        r.nombre AS rifa_nombre,
        r.precio_ticket,
        COALESCE(SUM(t.precio_pagado), 0) AS total_recaudado,
        COUNT(t.id) AS cantidad_tickets,
        p_fecha_desde AS fecha_desde,
        p_fecha_hasta AS fecha_hasta
    FROM rifas r
    LEFT JOIN tickets t ON t.rifa_id = r.id
        AND t.sede_id = r.sede_id
        AND t.estado IN ('APROBADO', 'PARTICIPANDO', 'GANADOR')
        AND DATE(t.fecha_compra) BETWEEN p_fecha_desde AND p_fecha_hasta
    WHERE r.sede_id = p_sede_id
      AND r.id = p_rifa_id
    GROUP BY r.id, r.codigo, r.nombre, r.precio_ticket, p_fecha_desde, p_fecha_hasta;
END //

-- 2. REPORTE GANADORES DE UNA RIFA (detalle para el reporte)
DROP PROCEDURE IF EXISTS reporte_ganadores_rifa //
CREATE PROCEDURE reporte_ganadores_rifa (
    IN p_sede_id INT,
    IN p_rifa_id INT
)
BEGIN
    SELECT
        g.id,
        g.rifa_id,
        g.nombre_completo,
        g.documento_completo,
        g.email,
        g.telefono,
        g.fecha_ganador,
        g.intento_ganador,
        g.jugado_por,
        pr.nombre AS premio_nombre,
        pr.codigo AS premio_codigo,
        COALESCE(rp.titulo, pr.nombre) AS premio_titulo,
        rp.orden AS premio_orden,
        (SELECT nr.numero_formateado
         FROM numeros_rifa nr
         WHERE nr.id = g.numero_id
         LIMIT 1) AS numero_ganador,
        (SELECT GROUP_CONCAT(t.codigo_ticket ORDER BY t.id SEPARATOR ', ')
         FROM tickets t
         WHERE t.persona_id = g.persona_id
           AND t.rifa_id = g.rifa_id
           AND t.estado = 'GANADOR') AS tickets_ganadores
    FROM ganadores g
    INNER JOIN premios pr ON g.premio_id = pr.id
    INNER JOIN rifas_premios rp ON g.rifa_premio_id = rp.id
    WHERE g.sede_id = p_sede_id
      AND g.rifa_id = p_rifa_id
    ORDER BY rp.orden ASC, g.fecha_ganador ASC;
END //

DELIMITER ;
