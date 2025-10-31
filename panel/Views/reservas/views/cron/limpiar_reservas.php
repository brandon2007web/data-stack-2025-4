<?php
// =============================================
// Script: limpiar_reservas.php
// Objetivo: eliminar automáticamente reservas
// que ya pasaron (fecha + hora final menor a ahora)
// =============================================

include(__DIR__ . "/../../../../../conexion.php");

// SQL: elimina las reservas vencidas (fecha fin anterior a ahora)
$sql = "DELETE FROM reserva WHERE CONCAT(Fecha_Fin, ' ', Hora_Inicio) < NOW()";

if ($conn->query($sql)) {
    echo "[" . date('Y-m-d H:i:s') . "] ✅ Reservas vencidas eliminadas correctamente. Filas afectadas: " . $conn->affected_rows . "\n";
} else {
    echo "[" . date('Y-m-d H:i:s') . "] ❌ Error al eliminar reservas: " . $conn->error . "\n";
}

$conn->close();
?>
