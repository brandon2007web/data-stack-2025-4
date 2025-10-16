<?php
session_start();
// 1. Requerir el archivo de conexión (se asume que contiene $conn)
require "conexion.php"; 

// =========================================================
// 0. MANEJO DE MENSAJES DE SESIÓN
// =========================================================
$message_text = '';
$message_type = '';

if (isset($_SESSION['message'])) {
    $message_text = $_SESSION['message']['text'];
    $message_type = $_SESSION['message']['type'];
    unset($_SESSION['message']);
}

// =========================================================
// 1. PROCESAMIENTO: CREAR AULA (POST)
// =========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'])) {
    
    // 1.1. Obtener y sanitizar datos
    $nombre = trim($_POST['nombre']);

    // 1.2. Validación básica
    if (empty($nombre)) {
        $message_text = "❌ Error: El nombre del aula no puede estar vacío.";
        $message_type = 'error';
    } else {
        // 1.3. Sentencia preparada para SEGURIDAD
        $sql = "INSERT INTO espacio (Nombre) VALUES (?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Vincular el parámetro (s = string)
            $stmt->bind_param("s", $nombre);

            // 1.4. Ejecutar la consulta
            if ($stmt->execute()) {
                // 1.5. Configurar mensaje de éxito en la sesión
                $_SESSION['message'] = [
                    'text' => "✅ Aula **" . htmlspecialchars($nombre) . "** creada con éxito.",
                    'type' => 'success'
                ];
                
                // 1.6. IMPLEMENTAR PATRÓN PRG (Redirección)
                header("Location: aulas.php"); 
                exit();
            } else {
                $message_text = "❌ Error al crear el aula: " . $stmt->error;
                $message_type = 'error';
            }

            $stmt->close();
        } else {
            $message_text = "❌ Error de preparación de la consulta: " . $conn->error;
            $message_type = 'error';
        }
    }
}

// =========================================================
// 2. OBTENER DATOS: MOSTRAR AULAS (GET)
// =========================================================
$aulas = [];
$result = $conn->query("SELECT ID_Espacio, Nombre FROM espacio ORDER BY Nombre ASC");
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $aulas[] = $row;
        }
    }
} else {
    // Manejo de error de consulta SELECT
    $message_text = "❌ Error al cargar las aulas: " . $conn->error;
    $message_type = 'error';
}

// Cerrar conexión si no se va a usar más
// $conn->close(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Aulas</title>
    <style>
        /* Estilos base */
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding-top: 80px; }
        header { 
            position: fixed; top: 0; left: 0; /* Corregido: left debe ser 0 */
            width: 100%; background: white; border-bottom: 1px solid #ddd; z-index: 1000; 
        }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        
        /* Estilos de formulario */
        .form-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 30px; }
        input[type="text"] {
            padding: 10px; border: 1px solid #ccc; border-radius: 4px; 
            margin-right: 10px; width: 60%; max-width: 300px; 
        }
        button[type="submit"] {
            padding: 10px 15px; background: #007bff; color: white; border: none; 
            border-radius: 4px; cursor: pointer; transition: background 0.3s;
        }
        button[type="submit"]:hover { background: #0056b3; }

        /* Estilos de listado */
        .aula-list { margin-top: 20px; }
        .aula {
            background: white; padding: 15px; margin: 10px 0; border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #007bff;
        }
        .aula a { text-decoration: none; color: #007bff; font-weight: bold; font-size: 1.1em; }
        .aula a:hover { text-decoration: underline; }
        
        /* Estilos de mensajes */
        .message { padding: 10px 20px; margin-bottom: 20px; border-radius: 4px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>

<body>
    <?php include("../header.php") ?>

    <div class="container">
        <h1>Administración de Aulas</h1>

        <?php if ($message_text): ?>
            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <h2>Crear Nueva Aula</h2>
            <form method="POST" action="aulas.php">
                <input type="text" name="nombre" placeholder="Ej: Aula 301 / Laboratorio de Redes" required maxlength="100">
                <button type="submit">Crear Aula</button>
            </form>
        </div>
        
        <div class="aula-list">
            <h2>Listado de Aulas Disponibles (<?php echo count($aulas); ?>)</h2>
            <?php
            if (count($aulas) > 0) {
                foreach ($aulas as $row) {
                    echo "<div class='aula'><a href='recursos.php?id=" . htmlspecialchars($row['ID_Espacio']) . "'>" . htmlspecialchars($row['Nombre']) . "</a></div>";
                }
            } else {
                echo "<p>No hay aulas creadas en la base de datos.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>