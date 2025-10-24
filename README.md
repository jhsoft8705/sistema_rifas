# CAFED Asistencia
## Integracion con Biometrico EQUIPO:ZKTECO UFACE8000 PLUS
## usuario:cafedasistencia2025
## pwd:cafed2025O
netstat -an | findstr :1433

USER LOCAL
userbiometrico
Bio123

## Restaurar bd biotime202510011439.bak a bd local nombre "biotime"
# paso 1 mover el backup a la ruta C:\Program Files\Microsoft SQL Server\MSSQL16.SQLEXPRESS\MSSQL\Backup\biotime202510011439.bak
# paso 2 ejecutar el script

USE master;
GO

-- Forzar modo de usuario único (cierra conexiones abiertas)
ALTER DATABASE biotime SET SINGLE_USER WITH ROLLBACK IMMEDIATE;
GO

-- Restaurar la base de datos
RESTORE DATABASE biotime
FROM DISK = 'C:\Program Files\Microsoft SQL Server\MSSQL16.SQLEXPRESS\MSSQL\Backup\biotime202510011439.bak'
WITH REPLACE,
MOVE 'biotime' TO 'C:\Program Files\Microsoft SQL Server\MSSQL16.SQLEXPRESS\MSSQL\DATA\biotime.mdf',
MOVE 'biotime_log' TO 'C:\Program Files\Microsoft SQL Server\MSSQL16.SQLEXPRESS\MSSQL\DATA\biotime.ldf';
GO

-- Devolver a multiusuario
ALTER DATABASE biotime SET MULTI_USER;
GO
## Ver Dumps de tablas
USE biotime;
GO

SELECT 
    t.name AS NombreTabla,
    s.name AS Esquema,
    t.create_date AS FechaCreacion,
    t.modify_date AS FechaModificacion
FROM sys.tables t
INNER JOIN sys.schemas s ON t.schema_id = s.schema_id
ORDER BY s.name, t.name;


## epbg@hotmail.com
## A0W6HG


## Resetear contador identity
DBCC CHECKIDENT ('cargos', RESEED, 0);



## Verificar que el trigger está activo
SELECT
    name AS trigger_name,
    OBJECT_NAME (parent_id) AS tabla,
    is_disabled,
    CASE is_disabled
        WHEN 0 THEN '✓ ACTIVO'
        ELSE '✗ DESHABILITADO'
    END AS estado
FROM
    sys.triggers
WHERE
    name = 'trg_sync_marcacion_directa';

GO


# belleza_barberia_manager
