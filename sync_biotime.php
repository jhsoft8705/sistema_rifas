<?php
/**
 * Script de Sincronización BioTime Local -> Remoto
 * Ejecutar cada 5-10 minutos via cron job
 */

class BioTimeSync {
    private $localDb;
    private $remoteDb;
    
    public function __construct() {
        $this->connectDatabases();
    }
    
    private function connectDatabases() {
        try {
            // Conexión a BD Local (BioTime)
            $this->localDb = new PDO(
                "sqlsrv:server=127.0.0.1\SQLEXPRESS;Database=biotime_local_testing",
                "userbiometrico", 
                "tu_password_aqui"
            );
            
            // Conexión a BD Remota (tu sistema)
            $this->remoteDb = new PDO(
                "sqlsrv:server=198.72.127.152;Database=db_control_asistencia_testing",
                "cafedasistencia", 
                "^wcbjo&1lpI6I0Ii"
            );
            
        } catch (Exception $e) {
            error_log("Error conexión BD: " . $e->getMessage());
            die("Error de conexión");
        }
    }
    
    public function syncEmployees() {
        try {
            // Obtener empleados desde BioTime local
            $stmt = $this->localDb->query("SELECT * FROM personnel_employee WHERE status = 0");
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($employees as $emp) {
                // Sincronizar con BD remota
                $this->syncEmployeeToRemote($emp);
            }
            
            echo "Empleados sincronizados: " . count($employees) . "\n";
            
        } catch (Exception $e) {
            error_log("Error sincronización empleados: " . $e->getMessage());
        }
    }
    
    public function syncAttendance() {
        try {
            // Obtener marcaciones desde BioTime local
            $stmt = $this->localDb->query("SELECT * FROM iclock_transaction WHERE sync_status = 0");
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($transactions as $trans) {
                // Sincronizar con BD remota
                $this->syncTransactionToRemote($trans);
                
                // Marcar como sincronizado
                $updateStmt = $this->localDb->prepare("UPDATE iclock_transaction SET sync_status = 1 WHERE id = ?");
                $updateStmt->execute([$trans['id']]);
            }
            
            echo "Marcaciones sincronizadas: " . count($transactions) . "\n";
            
        } catch (Exception $e) {
            error_log("Error sincronización marcaciones: " . $e->getMessage());
        }
    }
    
    private function syncEmployeeToRemote($employee) {
        try {
            // Verificar si el empleado ya existe en BD remota
            $checkStmt = $this->remoteDb->prepare("SELECT id FROM empleados WHERE nro_documento = ? AND sede_id = 1");
            $checkStmt->execute([$employee['emp_code']]);
            $existing = $checkStmt->fetch();
            
            if ($existing) {
                // Actualizar empleado existente
                $sql = "UPDATE empleados SET 
                        nombre = ?, 
                        apellido_paterno = ?, 
                        estado = ?,
                        fecha_modificacion = GETDATE()
                        WHERE nro_documento = ? AND sede_id = 1";
                
                $stmt = $this->remoteDb->prepare($sql);
                $stmt->execute([
                    $employee['first_name'],
                    $employee['last_name'],
                    ($employee['status'] == 0 ? 1 : 0), // Invertir status (0=activo en BioTime, 1=activo en sistema)
                    $employee['emp_code']
                ]);
            } else {
                // Insertar nuevo empleado
                $sql = "INSERT INTO empleados (
                    sede_id, nro_documento, nombre, apellido_paterno, 
                    fecha_ingreso, estado, fecha_creacion, creado_por
                ) VALUES (1, ?, ?, ?, GETDATE(), ?, GETDATE(), 'SYNC_BIOTIME')";
                
                $stmt = $this->remoteDb->prepare($sql);
                $stmt->execute([
                    $employee['emp_code'],
                    $employee['first_name'],
                    $employee['last_name'],
                    ($employee['status'] == 0 ? 1 : 0)
                ]);
            }
        } catch (Exception $e) {
            error_log("Error sincronizando empleado " . $employee['emp_code'] . ": " . $e->getMessage());
        }
    }
    
    private function syncTransactionToRemote($transaction) {
        try {
            // Buscar empleado en BD remota por código
            $empStmt = $this->remoteDb->prepare("SELECT id FROM empleados WHERE nro_documento = ? AND sede_id = 1");
            $empStmt->execute([$transaction['emp_code']]);
            $employee = $empStmt->fetch();
            
            if (!$employee) {
                error_log("Empleado no encontrado para código: " . $transaction['emp_code']);
                return;
            }
            
            $empleado_id = $employee['id'];
            $fecha_marcacion = date('Y-m-d', strtotime($transaction['punch_time']));
            $hora_marcacion = date('H:i:s', strtotime($transaction['punch_time']));
            
            // Determinar tipo de marcación basado en punch_state
            // punch_state: 1=entrada, 2=salida (según documentación BioTime)
            $tipo_marcacion = ($transaction['punch_state'] == 1) ? 'E' : 'S';
            
            // Verificar si ya existe una marcación para este empleado en esta fecha
            $checkStmt = $this->remoteDb->prepare("
                SELECT id, hora_entrada, hora_salida 
                FROM marcaciones 
                WHERE empleado_id = ? AND fecha_marcacion = ? AND sede_id = 1
            ");
            $checkStmt->execute([$empleado_id, $fecha_marcacion]);
            $existing = $checkStmt->fetch();
            
            if ($existing) {
                // Actualizar marcación existente
                if ($tipo_marcacion == 'E') {
                    $sql = "UPDATE marcaciones SET 
                            hora_entrada = ?, 
                            fecha_modificacion = GETDATE(),
                            modificado_por = 'SYNC_BIOTIME'
                            WHERE id = ?";
                    $stmt = $this->remoteDb->prepare($sql);
                    $stmt->execute([$hora_marcacion, $existing['id']]);
                } else {
                    $sql = "UPDATE marcaciones SET 
                            hora_salida = ?, 
                            fecha_modificacion = GETDATE(),
                            modificado_por = 'SYNC_BIOTIME'
                            WHERE id = ?";
                    $stmt = $this->remoteDb->prepare($sql);
                    $stmt->execute([$hora_marcacion, $existing['id']]);
                }
            } else {
                // Crear nueva marcación
                if ($tipo_marcacion == 'E') {
                    $sql = "INSERT INTO marcaciones (
                        sede_id, empleado_id, fecha_marcacion, tipo_marcacion, 
                        hora_entrada, fuente, fecha_creacion, creado_por
                    ) VALUES (1, ?, ?, ?, ?, 'BIOMETRICO', GETDATE(), 'SYNC_BIOTIME')";
                    $stmt = $this->remoteDb->prepare($sql);
                    $stmt->execute([$empleado_id, $fecha_marcacion, $tipo_marcacion, $hora_marcacion]);
                } else {
                    $sql = "INSERT INTO marcaciones (
                        sede_id, empleado_id, fecha_marcacion, tipo_marcacion, 
                        hora_salida, fuente, fecha_creacion, creado_por
                    ) VALUES (1, ?, ?, ?, ?, 'BIOMETRICO', GETDATE(), 'SYNC_BIOTIME')";
                    $stmt = $this->remoteDb->prepare($sql);
                    $stmt->execute([$empleado_id, $fecha_marcacion, $tipo_marcacion, $hora_marcacion]);
                }
            }
        } catch (Exception $e) {
            error_log("Error sincronizando marcación: " . $e->getMessage());
        }
    }
    
    public function run() {
        echo "Iniciando sincronización...\n";
        $this->syncEmployees();
        $this->syncAttendance();
        echo "Sincronización completada.\n";
    }
}

// Ejecutar sincronización
$sync = new BioTimeSync();
$sync->run();
?>
