<?php
session_start();
include "../../conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../IniciarSesion/iniciarsesion.php");
    exit();
}



// ===========================================
// 1️⃣ OBTENER DATOS ACTUALES DEL USUARIO
// ===========================================
$sql = "SELECT Nombre, Apellido, Correo, Documento FROM usuario WHERE ID_Usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// ===========================================
// 2️⃣ ACTUALIZAR DATOS SI SE ENVÍA EL FORM
// ===========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $documento = $_POST['documento'] ?? '';

    // Validación básica
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($documento)) {
        $message = "❌ Todos los campos son obligatorios.";
    } else {
        $sql = "UPDATE usuario 
                SET Nombre = ?, Apellido = ?, Correo = ?, Documento = ?
                WHERE ID_Usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $apellido, $correo, $documento, $usuario_id);

        if ($stmt->execute()) {
            $message = "✅ Datos actualizados correctamente.";
            $_SESSION['nombre'] = $nombre; // actualizar sesión
        } else {
            $message = "❌ Error al actualizar: " . $conn->error;
        }

        $stmt->close();
    }
}

// ===========================================
// 3️⃣ CERRAR CONEXIÓN
// ===========================================
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Configuración de Usuario</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 20px; }
    .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
    label { display: block; margin-top: 10px; font-weight: bold; }
    input { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
    button { margin-top: 15px; padding: 10px 15px; background: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #004999; }
    .message { margin-top: 10px; padding: 10px; border-radius: 5px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
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
      <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['Nombre']) ?>" required>

      <label>Apellido:</label>
      <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['Apellido']) ?>" required>

      <label>Correo:</label>
      <input type="email" name="correo" value="<?= htmlspecialchars($usuario['Correo']) ?>" required>

      <label>Documento:</label>
      <input type="text" name="documento" value="<?= htmlspecialchars($usuario['Documento']) ?>" required>

      <button type="submit">Guardar Cambios</button>
    </form>

    <p style="margin-top:15px;"><a href="../index.php">⬅️ Volver al Panel</a></p>
  </div>

</body>
</html>
