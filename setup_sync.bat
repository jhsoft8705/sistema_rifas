@echo off
REM Script para configurar sincronización automática en Windows
REM Ejecutar como administrador

echo Configurando tarea de sincronización BioTime...

REM Crear tarea programada que ejecute cada 5 minutos
schtasks /create /tn "BioTimeSync" /tr "C:\xampp\php\php.exe C:\xampp\htdocs\CONTROL_ASISTENCIA_CAFED\sync_biotime.php" /sc minute /mo 5 /ru SYSTEM

echo Tarea creada exitosamente.
echo La sincronización se ejecutará cada 5 minutos.

pause
