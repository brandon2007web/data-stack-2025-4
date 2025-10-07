<?php
session_start();

// Variables de estado
$message_text = '';
$message_type = '';
$asignaturas = []; // Cambiado a $asignaturas

// =========================================================
// 1. CONEXIÓN A LA BASE DE DATOS
// =========================================================
include "../../conexion.php"; 

if (!isset($conn) || $conn->connect_error) {
    $message_type = 'error';
    $message_text = "❌ Error de conexión a MySQL. Verifique XAMPP y la BD (proyecto_its).: " . ($conn->connect_error ?? 'Error desconocido.');
}

// =========================================================
// 2. LÓGICA DE PROCESAMIENTO (Crear y Eliminar Asignatura)
// =========================================================
if (isset($conn) && !$conn->connect_error) {

    // --- LÓGICA PARA CREAR NUEVA ASIGNATURA ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create_asignatura') { // Renombrado action
        $nombre_asignatura = $conn->real_escape_string($_POST['nombre_asignatura'] ?? ''); // Renombrado variable
        
        if (empty($nombre_asignatura)) {
            $message_type = 'error';
            $message_text = "❌ El nombre de la asignatura no puede estar vacío."; // Texto actualizado
        } else {
            // USANDO LA TABLA 'asignatura'
            $sql = "INSERT INTO asignatura (Nombre) VALUES (?)"; // TABLA ACTUALIZADA
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $nombre_asignatura);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "✅ Asignatura '{$nombre_asignatura}' creada con éxito."; // Texto actualizado
                    // Redirigir para usar el patrón PRG y limpiar el POST
                    header("Location: administrar-materias.php");
                    exit();
                } else {
                    $message_type = 'error';
                    $message_text = "❌ Error al crear la asignatura: " . $stmt->error; // Texto actualizado
                }
                $stmt->close();
            } else {
                $message_type = 'error';
                $message_text = "❌ Error de preparación de SQL (INSERT): " . $conn->error;
            }
        }
    }

    // --- LÓGICA PARA ELIMINAR ASIGNATURA ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_asignatura') { // Renombrado action
        $id_asignatura = intval($_POST['id_asignatura'] ?? 0); // ACTUALIZADO: Usando id_asignatura como nombre de campo
        
        if ($id_asignatura > 0) {
            // USANDO LA TABLA 'asignatura'
            // Nota: En un sistema real, primero verificarías si la asignatura tiene docentes o grupos asignados.
            $sql = "DELETE FROM asignatura WHERE ID_Asignatura = ?"; // ACTUALIZADO: PK es ID_Asignatura
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $id_asignatura); // Usando la variable renombrada
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "✅ Asignatura eliminada con éxito."; // Texto actualizado
                    // Redirigir para usar el patrón PRG
                    header("Location: administrar-materias.php");
                    exit();
                } else {
                    $message_type = 'error';
                    $message_text = "❌ Error al eliminar la asignatura: " . $stmt->error; // Texto actualizado
                }
                $stmt->close();
            } else {
                $message_type = 'error';
                $message_text = "❌ Error de preparación de SQL (DELETE): " . $conn->error;
            }
        }
    }
}

// =========================================================
// 3. RECUPERACIÓN DE DATOS (Listar todas las Asignaturas)
// =========================================================
if (isset($conn) && !$conn->connect_error) {
    
    // Consulta para listar todas las asignaturas
    // USANDO LA TABLA 'asignatura'
    $sql = "SELECT ID_Asignatura, Nombre FROM asignatura ORDER BY Nombre ASC"; // ACTUALIZADO: Seleccionando ID_Asignatura
    
    if ($result = $conn->query($sql)) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $asignaturas[] = $row; // Usando el nuevo array $asignaturas
            }
        }
        $result->free();
    } else {
        // Solo sobrescribe el mensaje si no hay un mensaje de procesamiento más importante.
        if (empty($message_text)) {
            $message_type = 'error';
            $message_text = "❌ Error al listar las asignaturas: " . $conn->error . ". Asegúrese de que la tabla 'asignatura' exista."; // Texto actualizado
        }
    }
    
    // Cerrar conexión
    $conn->close();
}

// =========================================================
// 4. MANEJO DE MENSAJES DE SESIÓN (PRG Pattern)
// =========================================================
if (isset($_SESSION['success_message'])) {
    $message_text = $_SESSION['success_message'];
    $message_type = 'success';
    unset($_SESSION['success_message']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administración de Asignaturas</title>
    <style>
        :root{--bg:#f7f7fb; --card:#fff; --text:#12141a; --muted:#60646c;--brand:#4f46e5; --brand-2:#4338ca; --line:#e5e7eb; --radius:16px;}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto}
        .container{max-width:900px;margin:0 auto;padding:28px 16px}
        .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 4px 6px rgba(0,0,0,.05);padding:30px; margin-top: 20px;}
        h1{color:var(--text);margin-bottom:10px;text-align:center;}
        h2{color:var(--text); margin-top: 0; border-bottom: 1px solid var(--line); padding-bottom: 10px; margin-bottom: 20px;}
        .back-link{display:block;margin-bottom:20px;text-align:center;color:var(--brand);text-decoration:none;font-weight:500;}

        /* Formulario y Botones */
        .form-group{margin-bottom:15px;}
        label{display:block;margin-bottom:5px;font-weight:600;font-size:14px;color:var(--muted);}
        input[type="text"]{width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:12px;font-size:16px;transition:border-color .3s;}
        input:focus{outline:none;border-color:var(--brand);}
        .btn{border:0;border-radius:12px;background:var(--brand);color:#fff;padding:12px 18px;font-weight:600;cursor:pointer;transition:background .3s;}
        .btn-full{width:100%;}
        .btn:hover{background:var(--brand-2);}
        .btn-delete {background: #dc2626; margin-left: 10px; padding: 6px 12px;}
        .btn-delete:hover {background: #b91c1c;}
        
        /* Mensajes */
        #message{margin-top:20px;padding:15px;border-radius:12px;text-align:center;display:block; font-weight: 600;}
        .success{background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;}
        .error{background:#f8d7da;color:#842029;border:1px solid #f5c2c7;}
        .info{background:#cfe2ff;color:#05367b;border:1px solid #b3d7ff;}

        /* Tabla de listado */
        .table-container {overflow-x: auto; margin-top: 30px;}
        table {width: 100%; border-collapse: collapse; border-radius: 12px; overflow: hidden;}
        th, td {padding: 14px 15px; text-align: left;}
        th {background-color: var(--brand); color: #fff; font-weight: 600; font-size: 15px;}
        tr:nth-child(even) {background-color: #f1f1f5;}
        tr:hover {background-color: #e5e5f0; transition: background-color 0.3s;}
        .action-cell {display: flex; justify-content: flex-end;}
    </style>
</head>
<body>
    <div class="container">
        <!-- Enlace para volver al panel de docentes (que es el panel principal de administración) -->
        <a href="docentescreado.php" class="back-link">← Volver al Panel de Docentes</a>

        <h1>Administración de Asignaturas</h1> <!-- Título actualizado -->
        <p style="text-align: center; color: var(--muted); margin-bottom: 25px;">Cree, edite y gestione las asignaturas que serán impartidas por los docentes.</p>

        <?php if ($message_text): ?>
            <div id="message" class="<?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Crear Nueva Asignatura</h2> <!-- Título actualizado -->
            <form method="POST" action="">
                <input type="hidden" name="action" value="create_asignatura"> <!-- action actualizado -->
                <div class="form-group">
                    <label for="nombre_asignatura">Nombre de la Asignatura</label> <!-- Texto y for actualizado -->
                    <input type="text" name="nombre_asignatura" id="nombre_asignatura" required placeholder="Ej: Introducción a la Programación"> <!-- name y id actualizado -->
                </div>
                <button type="submit" class="btn btn-full">Crear Asignatura</button> <!-- Texto actualizado -->
            </form>
        </div>

        <div class="card">
            <h2>Listado de Asignaturas Existentes (<?php echo count($asignaturas); ?>)</h2> <!-- Título y count actualizado -->

            <?php if (count($asignaturas) > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre de la Asignatura</th> <!-- Título actualizado -->
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asignaturas as $asignatura): ?> <!-- Loop variable actualizado -->
                            <tr>
                                <td><?php echo htmlspecialchars($asignatura['ID_Asignatura']); ?></td> <!-- ACTUALIZADO: ID_Asignatura -->
                                <td><?php echo htmlspecialchars($asignatura['Nombre']); ?></td>
                                <td class="action-cell">
                                    <!-- Botón de eliminar con confirmación y POST -->
                                    <form method="POST" action="" onsubmit="return confirm('¿Está seguro de que desea eliminar la asignatura «<?php echo htmlspecialchars($asignatura['Nombre']); ?>»? Esta acción es irreversible.');">
                                        <input type="hidden" name="action" value="delete_asignatura"> <!-- action actualizado -->
                                        <input type="hidden" name="id_asignatura" value="<?php echo htmlspecialchars($asignatura['ID_Asignatura']); ?>"> <!-- ACTUALIZADO: id_asignatura -->
                                        <button type="submit" class="btn btn-delete">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                 <?php if (empty($message_text) || $message_type == 'info'): ?>
                    <p style="text-align: center; padding: 20px; font-style: italic; color: var(--muted);">No hay asignaturas registradas. Utilice el formulario de arriba para agregar una.</p> <!-- Texto actualizado -->
                <?php endif; ?>
            <?php endif; ?>
            
        </div>
    </div>
    <!-- Script para ocultar mensaje de éxito de sesión después de 5 segundos -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const messageDiv = document.getElementById('message');
            if (messageDiv && messageDiv.classList.contains('success')) {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
</html>
