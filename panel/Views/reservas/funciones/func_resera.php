<?php
session_start();
include("../../../conexion.php");

// Inicializar variables de mensaje
$message_text = '';
$message_type = '';

// Verificar si hay mensaje en la sesión
if (isset($_SESSION['message'])) {
    $message_text = $_SESSION['message']['text'];
    $message_type = $_SESSION['message']['type'];
    unset($_SESSION['message']); // evitar mostrarlo en recarga
}

// === 1. Obtener recursos ===
$recursos_disponibles = [];
$sql_recursos = "SELECT ID_Recurso, Nombre, Estado FROM recursos ORDER BY Nombre ASC";
$result_recursos = $conn->query($sql_recursos);
if ($result_recursos) {
    while ($row = $result_recursos->fetch_assoc()) {
        $recursos_disponibles[] = $row;
    }
}

// === 2. Obtener aulas ===
$aulas_disponibles = [];
$sql_aulas = "SELECT ID_Aula, Nombre, ID_Piso FROM aulas ORDER BY Nombre ASC";
$result_aulas = $conn->query($sql_aulas);
if ($result_aulas) {
    while ($row = $result_aulas->fetch_assoc()) {
        $aulas_disponibles[] = $row;
    }
}

// === 3. Usuario actual (de sesión) ===
$id_usuario_actual = $_SESSION['usuario_id'] ?? null;
?>