<?php
session_start();
$message = "";

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: /IniciarSesion/iniciarsesion.php");
    exit();
}

// Variables de sesión, si el ID no existe, se inicializa a 0
$usuario_id = $_SESSION['usuario_id'] ?? 0;
$nombre     = $_SESSION['nombre'] ?? '';
$apellido   = $_SESSION['apellido'] ?? '';
$correo     = $_SESSION['correo'] ?? '';

include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_nuevo   = $_POST['nombre'] ?? '';
    $apellido_nuevo = $_POST['apellido'] ?? '';
    $correo_nuevo   = $_POST['correo'] ?? '';

    // 1. Validar si el ID de usuario es válido antes de proceder
    if ($usuario_id === 0) {
        $message = "❌ Error: ID de usuario no encontrado en la sesión. Inicie sesión nuevamente.";
    } else {
        // Actualizar registro existente
        $stmt_update = $conn->prepare(
            "UPDATE usuario SET Nombre = ?, Apellido = ?, Correo = ? WHERE ID_Usuario = ?"
        );

        if ($stmt_update) {
            // CORRECCIÓN CLAVE: Agregamos 'i' para indicar que ID_Usuario es un entero, 
            // y pasamos la variable $usuario_id al final.
            if ($stmt_update->bind_param("sssi", $nombre_nuevo, $apellido_nuevo, $correo_nuevo, $usuario_id)) {

                if ($stmt_update->execute()) { // <--- LA BD SE ACTUALIZA AQUÍ
                    $message = "✅ Datos actualizados correctamente.";

                    // Actualizar sesión y variables (SOLO DESPUÉS DE LA BD)
                    $_SESSION['nombre']   = $nombre_nuevo;
                    $_SESSION['apellido'] = $apellido_nuevo;
                    $_SESSION['correo']   = $correo_nuevo;

                    $nombre   = $nombre_nuevo;
                    $apellido = $apellido_nuevo;
                    $correo   = $correo_nuevo;
                } else {
                    $message = "❌ Error al actualizar: " . $stmt_update->error;
                }
            } else {
                 $message = "❌ Error al enlazar parámetros: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $message = "❌ Error en la preparación de la consulta: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Configuración de Usuario</title>
<style>
:root {
    --primary: #2563eb;
    --primary-hover: #1e40af;
    --bg: #f0f2f5;
    --white: #ffffff;
    --shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    --success-bg: #d1fae5;
    --success-text: #065f46;
    --error-bg: #fee2e2;
    --error-text: #991b1b;
}
* { box-sizing: border-box; }
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: var(--bg);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}
.container {
    background: var(--white);
    padding: 30px;
    border-radius: 12px;
    box-shadow: var(--shadow);
    width: 100%;
    max-width: 450px;
    animation: fadeIn 0.5s ease-in-out;
}
h2 { text-align: center; color: var(--primary); margin-bottom: 20px; }
label { display: block; margin-top: 15px; font-weight: 600; color: #333; }
input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}
input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 5px rgba(37, 99, 235, 0.3);
}
button {
    width: 100%;
    padding: 12px;
    margin-top: 20px;
    background: var(--primary);
    color: white;
    border: none;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}
button:hover { background: var(--primary-hover); }
.message {
    margin-top: 15px;
    padding: 12px;
    border-radius: 8px;
    font-weight: 500;
    text-align: center;
}
.success { background: var(--success-bg); color: var(--success-text); }
.error { background: var(--error-bg); color: var(--error-text); }
.back-link {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}
.back-link:hover { color: var(--primary-hover); }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>

<div class="container">
    <h2>⚙️ Configuración de Usuario</h2>

    <?php if ($message): ?>
        <div class="message <?= str_contains($message, '✅') ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>">

        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?= htmlspecialchars($apellido) ?>">

        <label>Correo:</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($correo) ?>">

        <button type="submit">💾 Guardar Cambios</button>
    </form>

    <a href="../index.php" class="back-link">⬅️ Volver al Panel</a>
</div>

</body>
</html>
