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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Reserva</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="datetime-local"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .mensaje {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nueva Reserva</h1>

        <?php if ($message_text): ?>
            <div class="mensaje <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message_text); ?>
            </div>
        <?php endif; ?>

        <?php if ($id_usuario_actual === null): ?>
            <div class="mensaje error">
                ⚠️ Error: No se encontró el ID del usuario en la sesión. Por favor, inicie sesión antes de crear una reserva.
            </div>
        <?php else: ?>

        <form action="procesar_reserva.php" method="POST">

            <!-- ID del usuario en sesión -->
            <input type="hidden" name="ID_Usuario" value="<?php echo htmlspecialchars($id_usuario_actual); ?>">

            <label for="id_recurso">Recurso (Equipo):</label>
            <select id="id_recurso" name="ID_Recurso" required>
                <option value="">-- Seleccione un Recurso --</option>
                <?php foreach ($recursos_disponibles as $recurso): ?>
                    <option value="<?php echo htmlspecialchars($recurso['ID_Recurso']); ?>">
                        <?php echo htmlspecialchars($recurso['Nombre']) . " (Estado: " . htmlspecialchars($recurso['Estado']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_aulas">Aula / Sala:</label>
            <select id="id_aulas" name="ID_Aulas" required>
                <option value="">-- Seleccione un Aula --</option>
                <?php foreach ($aulas_disponibles as $aula): ?>
                    <option value="<?php echo htmlspecialchars($aula['ID_Aula']); ?>">
                        <?php echo htmlspecialchars($aula['Nombre']) . " (Piso: " . htmlspecialchars($aula['ID_Piso']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="inicio_reserva">Inicio de la Reserva:</label>
            <input type="datetime-local" id="inicio_reserva" name="Inicio_Reserva" required>

            <label for="fin_reserva">Fin de la Reserva:</label>
            <input type="datetime-local" id="fin_reserva" name="Fin_Reserva" required>

            <label for="proposito">Propósito / Motivo:</label>
            <textarea id="proposito" name="Descripcion_Motivo" rows="3" placeholder="Ejemplo: uso del proyector para clase de informática" required></textarea>

            <button type="submit">Confirmar Reserva</button>
        </form>

        <?php endif; ?>
    </div>
</body>
</html>
