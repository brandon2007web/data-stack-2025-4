<?php
session_start();

// Array para almacenar la lista de docentes
$docentes = [];
$message_text = '';
$message_type = '';

// =========================================================
// 1. CONEXIÓN Y EXTRACCIÓN DE DOCENTES (ID_Rol = 2)
// =========================================================

// Cargar el archivo de conexión externo.
// Se asume que este archivo (conexion.php) establece la variable $conn.
include "../../conexion.php"; 

// Verificar la conexión usando el objeto $conn proporcionado por conexion.php
if (!isset($conn) || $conn->connect_error) {
    $message_type = 'error';
    $connect_error_msg = isset($conn) ? $conn->connect_error : 'El archivo conexion.php no proporcionó el objeto $conn o no existe.';
    $message_text = "❌ Error de conexión a MySQL. Verifique XAMPP y la BD (proyecto_its).: " . $connect_error_msg;
} else {
    // Consulta SQL para seleccionar SOLO a los usuarios con ID_Rol = 2 (Docentes)
    // Se ordenan por Apellido para una lista más organizada.
    $sql = "SELECT ID_Usuario, Nombre, Apellido, Correo, Documento FROM usuario WHERE ID_Rol = 2 ORDER BY Apellido ASC";
    
    if ($result = $conn->query($sql)) {
        // Almacenar los resultados en el array $docentes
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Aquí simulamos que tiene 0 materias asignadas por defecto (esto se implementará después)
                $row['Materias_Asignadas'] = 0; 
                $docentes[] = $row;
            }
        } else {
             $message_type = 'info';
             $message_text = "ℹ️ No hay docentes registrados en la base de datos (ID_Rol = 2).";
        }
        $result->free();
    } else {
        $message_type = 'error';
        $message_text = "❌ Error al ejecutar la consulta: " . $conn->error;
    }
    
    // Cerrar conexión
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administración de Docentes</title>
    <style>
        :root{--bg:#f7f7fb; --card:#fff; --text:#12141a; --muted:#60646c;--brand:#4f46e5; --brand-2:#4338ca; --line:#e5e7eb; --radius:16px;}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto}
        .container{max-width:900px;margin:0 auto;padding:28px 16px}
        .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 4px 6px rgba(0,0,0,.05);padding:30px; margin-top: 20px;}
        h1{color:var(--text);margin-bottom:10px;text-align:center;}
        p{text-align: center; color: var(--muted); margin-bottom: 25px;}
        .back-link{display:block;margin-bottom:20px;text-align:center;color:var(--brand);text-decoration:none;font-weight:500;}
        
        /* Estilos de tabla */
        .table-container {overflow-x: auto;}
        table {width: 100%; border-collapse: collapse; margin-top: 20px; border-radius: 12px; overflow: hidden;}
        th, td {padding: 14px 15px; text-align: left;}
        th {background-color: var(--brand); color: #fff; font-weight: 600; font-size: 15px;}
        tr:nth-child(even) {background-color: #f1f1f5;}
        tr:hover {background-color: #e5e5f0; transition: background-color 0.3s;}
        .action-link {color: var(--brand); text-decoration: none; font-weight: 500;}
        .action-link:hover {text-decoration: underline;}
        
        #message{margin-top:20px;padding:15px;border-radius:12px;text-align:center;display:block; font-weight: 600;}
        .success{background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;}
        .error{background:#f8d7da;color:#842029;border:1px solid #f5c2c7;}
        .info{background:#cfe2ff;color:#05367b;border:1px solid #b3d7ff;}
         .back-links{display:block;margin-bottom:20px;text-align:center;color:var(--brand);text-decoration:none;font-weight:500;}
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: right;">
            <!-- Enlace para volver a crear un docente -->
              <a href="crear-docente.php" class="back-links">← Volver a crear Docentes</a>
            <a href="crear-docente.php" class="back-link" style="text-align: right; display: inline-block;">+ Crear Nuevo Docente</a>
        </div>

        <h1>Panel de Administración de Docentes</h1>
        <p>A continuación se muestra la lista de todos los docentes registrados. Utilice el botón "Administrar" para editar su perfil y asignar materias/grupos.</p>
        
        <?php if ($message_text): ?>
            <div id="message" class="<?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            
            <?php if (count($docentes) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Documento</th>
                                <th>Nombre Completo</th>
                                <th>Correo</th>
                                <th>Materias</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docentes as $docente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($docente['ID_Usuario']); ?></td>
                                <td><?php echo htmlspecialchars($docente['Documento']); ?></td>
                                <td><?php echo htmlspecialchars($docente['Nombre'] . ' ' . $docente['Apellido']); ?></td>
                                <td><?php echo htmlspecialchars($docente['Correo']); ?></td>
                                <!-- Por ahora, solo mostramos el contador de materias simulado -->
                                <td><?php echo $docente['Materias_Asignadas']; ?></td> 
                                <td>
                                    <!-- Este enlace llevará a la página de edición. Pasamos el ID del docente en la URL -->
                                    <a href="editar-docente.php?id=<?php echo htmlspecialchars($docente['ID_Usuario']); ?>" class="action-link">Administrar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Si no hay docentes Y no hubo un mensaje de error de conexión, mostramos un mensaje por defecto. -->
                <?php if (!$message_text): ?>
                    <p style="text-align: center; padding: 20px; font-style: italic;">No se encontraron docentes en la base de datos.</p>
                <?php endif; ?>
            <?php endif; ?>
            
        </div>
    </div>
</body>
</html>
