<?php
session_start();

// Obtener el ID del docente de la URL
$docente_id = intval($_GET['id'] ?? 0);
$docente = null;
$message_text = '';
$message_type = '';
$current_tab = $_GET['tab'] ?? 'perfil'; // Manejo de pestañas

// Listas para la asignación de asignaturas y grupos
$all_asignaturas = [];
$asignaturas_asignadas_ids = [];
$all_grupos = [];
$grupos_asignados_data = []; // Almacena {ID_Grupo, ID_Asignatura, Nombre_Asignatura, Nombre_Grupo}

// =========================================================
// 0. MANEJO DE MENSAJES DE SESIÓN (PRG Pattern)
// =========================================================
if (isset($_SESSION['success_message'])) {
    $message_text = $_SESSION['success_message'];
    $message_type = 'success';
    unset($_SESSION['success_message']);
}

// =========================================================
// 1. CONEXIÓN A LA BASE DE DATOS
// =========================================================
include "../../conexion.php"; 

if (!isset($conn) || $conn->connect_error) {
    die("❌ Error de conexión a MySQL. Verifique XAMPP y la BD (proyecto_its).: " . ($conn->connect_error ?? 'Error desconocido.'));
}

// =========================================================
// 2. RECUPERACIÓN INICIAL DE DATOS DEL DOCENTE
// =========================================================
if ($docente_id > 0) {
    $sql = "SELECT ID_Usuario, Nombre, Apellido, Correo, Documento FROM usuario WHERE ID_Usuario = ? AND ID_Rol = 2";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $docente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $docente = $result->fetch_assoc();
        } else {
            $message_type = 'error';
            $message_text = "❌ Docente no encontrado o ID de rol incorrecto.";
        }
        $stmt->close();
    }
} else {
    $message_type = 'error';
    $message_text = "❌ ID de Docente no válido.";
}

// Si no hay docente, detenemos la ejecución o redirigimos
if (!$docente && $message_type == 'error') {
    $conn->close();
    // No usamos die() para poder mostrar la interfaz con el mensaje de error.
}


// =========================================================
// 3. LÓGICA DE PROCESAMIENTO (POST)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $docente) {
    $action = $_POST['action'] ?? '';

    // --- SUBMIT: ACTUALIZAR PERFIL ---
    if ($action === 'update_profile') {
        // ... (Lógica de actualización de perfil, idéntica a la anterior)
        
        $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
        $apellido = $conn->real_escape_string($_POST['apellido'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $documento = $conn->real_escape_string($_POST['documento'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $updates = [];
        $params = [];
        $types = "";

        if (!empty($nombre)) { $updates[] = "Nombre = ?"; $params[] = $nombre; $types .= "s"; }
        if (!empty($apellido)) { $updates[] = "Apellido = ?"; $params[] = $apellido; $types .= "s"; }
        if (!empty($email)) { $updates[] = "Correo = ?"; $params[] = $email; $types .= "s"; }
        if (!empty($documento)) { $updates[] = "Documento = ?"; $params[] = $documento; $types .= "s"; }

        if (!empty($password)) {
            // Hashing si se proporciona una nueva contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $updates[] = "Contrasena = ?"; 
            $params[] = $hashed_password; 
            $types .= "s"; 
        }

        if (!empty($updates)) {
            $sql = "UPDATE usuario SET " . implode(", ", $updates) . " WHERE ID_Usuario = ?";
            
            $params[] = $docente_id;
            $types .= "i";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "✅ Perfil del docente actualizado con éxito.";
                } else {
                    $_SESSION['success_message'] = "❌ Error al actualizar el perfil: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $_SESSION['success_message'] = "❌ Error de preparación de SQL (UPDATE): " . $conn->error;
            }
            // PRG Redirección para el perfil
            header("Location: editar-docente.php?id={$docente_id}&tab=perfil");
            exit();
        }


    } 
    
    // --- SUBMIT: ASIGNAR ASIGNATURAS ---
    else if ($action === 'update_asignaturas') {
        
        // 1. Obtener las asignaturas seleccionadas
        $selected_asignaturas_ids = $_POST['asignaturas'] ?? [];
        $ids_a_asignar = array_map('intval', $selected_asignaturas_ids);
        
        // 2. Transacción de asignación/desasignación
        $conn->begin_transaction();
        $success = true;

        try {
            // A. Eliminar TODAS las asignaciones existentes para este docente en docente_asignatura
            $sql_delete = "DELETE FROM docente_asignatura WHERE ID_Usuario = ?";
            if ($stmt_del = $conn->prepare($sql_delete)) {
                $stmt_del->bind_param("i", $docente_id);
                $stmt_del->execute();
                $stmt_del->close();
            } else { $success = false; throw new Exception("Error DELETE DA: " . $conn->error); }

            // B. Insertar las NUEVAS asignaciones seleccionadas
            if (!empty($ids_a_asignar)) {
                $values_placeholders = [];
                $params_insert = [];
                $types_insert = '';

                foreach ($ids_a_asignar as $asignatura_id) {
                    $values_placeholders[] = "(?, ?)";
                    $params_insert[] = $docente_id;
                    $params_insert[] = $asignatura_id;
                    $types_insert .= "ii";
                }
                
                $sql_insert = "INSERT INTO docente_asignatura (ID_Usuario, ID_Asignatura) VALUES " . implode(", ", $values_placeholders);
                
                if ($stmt_ins = $conn->prepare($sql_insert)) {
                    $stmt_ins->bind_param($types_insert, ...$params_insert);
                    $stmt_ins->execute();
                    $stmt_ins->close();
                } else { $success = false; throw new Exception("Error INSERT DA: " . $conn->error); }
            }
            
            // 3. Confirmar o revertir la transacción
            if ($success) {
                $conn->commit();
                $_SESSION['success_message'] = "✅ Asignaturas del docente actualizadas con éxito.";
            } else {
                $conn->rollback();
                $_SESSION['success_message'] = "❌ Error en la transacción de asignación.";
            }

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['success_message'] = "❌ Error de asignación: " . $e->getMessage();
        }

        // PRG Redirección para asignaturas
        header("Location: editar-docente.php?id={$docente_id}&tab=asignaturas");
        exit();
    }
    
    // --- SUBMIT: ASIGNAR GRUPOS ---
    else if ($action === 'add_grupo_asignatura') {
        $id_grupo_new = intval($_POST['id_grupo_new'] ?? 0);
        $id_asignatura_new = intval($_POST['id_asignatura_new'] ?? 0);
        
        if ($id_grupo_new > 0 && $id_asignatura_new > 0) {
            
            // Verificación extra: Asegurar que la asignatura está permitida para este docente (en docente_asignatura)
            $check_sql = "SELECT ID_Asignatura FROM docente_asignatura WHERE ID_Usuario = ? AND ID_Asignatura = ?";
            if ($stmt_check = $conn->prepare($check_sql)) {
                $stmt_check->bind_param("ii", $docente_id, $id_asignatura_new);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                
                if ($result_check->num_rows > 0) {
                    // La asignatura está permitida. Procedemos a la inserción en docente_grupo.
                    $insert_sql = "INSERT INTO docente_grupo (ID_Usuario, ID_Grupo, ID_Asignatura) VALUES (?, ?, ?)";
                    if ($stmt_insert = $conn->prepare($insert_sql)) {
                        $stmt_insert->bind_param("iii", $docente_id, $id_grupo_new, $id_asignatura_new);
                        if ($stmt_insert->execute()) {
                            $_SESSION['success_message'] = "✅ Grupo y Asignatura asignados con éxito.";
                        } else {
                            // Error 1062 es para clave duplicada (ya asignado)
                            if ($stmt_insert->errno === 1062) {
                                $_SESSION['success_message'] = "⚠️ Esta combinación de Grupo y Asignatura ya está asignada al docente.";
                            } else {
                                $_SESSION['success_message'] = "❌ Error al asignar grupo: " . $stmt_insert->error;
                            }
                        }
                        $stmt_insert->close();
                    }
                } else {
                    $_SESSION['success_message'] = "❌ Error: La asignatura seleccionada no ha sido previamente asignada a este docente. Asignela en la pestaña 2.";
                }
                $stmt_check->close();
            }
        } else {
            $_SESSION['success_message'] = "❌ Error: Debe seleccionar Grupo y Asignatura válidos.";
        }

        // PRG Redirección para grupos
        header("Location: editar-docente.php?id={$docente_id}&tab=grupos");
        exit();
    }
    
    // --- SUBMIT: ELIMINAR ASIGNACIÓN DE GRUPO ---
    else if ($action === 'delete_grupo_asignatura') {
        $id_grupo_del = intval($_POST['id_grupo_del'] ?? 0);
        $id_asignatura_del = intval($_POST['id_asignatura_del'] ?? 0);
        
        if ($id_grupo_del > 0 && $id_asignatura_del > 0) {
            $delete_sql = "DELETE FROM docente_grupo WHERE ID_Usuario = ? AND ID_Grupo = ? AND ID_Asignatura = ?";
            if ($stmt_del = $conn->prepare($delete_sql)) {
                $stmt_del->bind_param("iii", $docente_id, $id_grupo_del, $id_asignatura_del);
                if ($stmt_del->execute()) {
                    $_SESSION['success_message'] = "✅ Asignación de grupo/asignatura eliminada.";
                } else {
                    $_SESSION['success_message'] = "❌ Error al eliminar asignación: " . $stmt_del->error;
                }
                $stmt_del->close();
            }
        } else {
            $_SESSION['success_message'] = "❌ ID de Grupo o Asignatura no válidos para eliminar.";
        }
        
        // PRG Redirección para grupos
        header("Location: editar-docente.php?id={$docente_id}&tab=grupos");
        exit();
    }
}


// =========================================================
// 4. RECUPERACIÓN DE DATOS ADICIONALES (PARA PESTAÑAS)
// =========================================================
if ($docente && $conn) {
    
    // --- Datos para la pestaña de ASIGNATURAS (Siempre necesarios) ---
    // A. Obtener TODAS las asignaturas disponibles
    $sql_all = "SELECT ID_Asignatura, Nombre FROM asignatura ORDER BY Nombre ASC";
    if ($result_all = $conn->query($sql_all)) {
        while ($row = $result_all->fetch_assoc()) {
            $all_asignaturas[$row['ID_Asignatura']] = $row; // Guardamos con ID como clave para fácil referencia
        }
        $result_all->free();
    }

    // B. Obtener las asignaturas YA asignadas a este docente
    $sql_assigned = "SELECT ID_Asignatura FROM docente_asignatura WHERE ID_Usuario = ?";
    if ($stmt_assigned = $conn->prepare($sql_assigned)) {
        $stmt_assigned->bind_param("i", $docente_id);
        $stmt_assigned->execute();
        $result_assigned = $stmt_assigned->get_result();
        while ($row = $result_assigned->fetch_assoc()) {
            $asignaturas_asignadas_ids[] = $row['ID_Asignatura'];
        }
        $stmt_assigned->close();
    }

    
    // --- Datos para la pestaña de GRUPOS ---
    if ($current_tab === 'grupos') {
        
        // C. Obtener TODOS los grupos disponibles, uniéndolos con el nombre del Curso (usando ID_Curso)
        $sql_grupos = "
            SELECT 
                g.ID_Grupo, 
                g.Nombre, 
                g.ID_Curso, 
                c.Nombre AS Nombre_Curso
            FROM grupo g
            JOIN curso c ON g.ID_Curso = c.ID_Curso 
            ORDER BY c.Nombre, g.Nombre ASC
        ";
        if ($result_grupos = $conn->query($sql_grupos)) {
            while ($row = $result_grupos->fetch_assoc()) {
                $all_grupos[$row['ID_Grupo']] = $row;
            }
            $result_grupos->free();
        }

        // D. Obtener las asignaciones de GRUPO y ASIGNATURA para este docente
        // Utilizamos JOIN con la tabla 'curso' para obtener el Nombre_Curso en lugar del Nivel
        $sql_assigned_groups = "
            SELECT 
                dg.ID_Grupo, 
                dg.ID_Asignatura, 
                g.Nombre AS Nombre_Grupo, 
                c.Nombre AS Nombre_Curso,
                a.Nombre AS Nombre_Asignatura
            FROM docente_grupo dg
            JOIN grupo g ON dg.ID_Grupo = g.ID_Grupo
            JOIN asignatura a ON dg.ID_Asignatura = a.ID_Asignatura
            JOIN curso c ON g.ID_Curso = c.ID_Curso
            WHERE dg.ID_Usuario = ?
            ORDER BY c.Nombre, g.Nombre, a.Nombre";

        if ($stmt_assigned_groups = $conn->prepare($sql_assigned_groups)) {
            $stmt_assigned_groups->bind_param("i", $docente_id);
            $stmt_assigned_groups->execute();
            $result_assigned_groups = $stmt_assigned_groups->get_result();
            while ($row = $result_assigned_groups->fetch_assoc()) {
                $grupos_asignados_data[] = $row;
            }
            $stmt_assigned_groups->close();
        }
    }
}

// La conexión se cerrará al final del script si no se hizo antes
if (isset($conn)) {
    // Nota: La conexión ya se cerró en la lógica POST exitosa (por el exit()), 
    // pero si llegamos aquí en GET o POST con error, la cerramos.
    if (!$docente) {
        $conn->close();
    }
}
// La contraseña solo se rellena si hay un error en POST (para que el usuario no la pierda)
$password_value = ($_SERVER['REQUEST_METHOD'] === 'POST' && $message_type === 'error' && $current_tab === 'perfil') ? $_POST['password'] ?? '' : '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administrar Docente: <?php echo htmlspecialchars($docente['Nombre'] ?? 'Docente Desconocido'); ?></title>
    <style>
        :root{--bg:#f7f7fb; --card:#fff; --text:#12141a; --muted:#60646c;--brand:#4f46e5; --brand-2:#4338ca; --line:#e5e7eb; --radius:16px;}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto}
        .container{max-width:800px;margin:0 auto;padding:28px 16px}
        .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 4px 6px rgba(0,0,0,.05);padding:30px; margin-top: 20px;}
        h1{color:var(--text);margin-bottom:10px;text-align:center;}
        h2{color:var(--text); margin-top: 0; padding-bottom: 10px; margin-bottom: 20px; font-size: 1.5rem;}
        .back-link{display:block;margin-bottom:20px;text-align:center;color:var(--brand);text-decoration:none;font-weight:500;}

        /* Tabs */
        .tabs { display: flex; border-bottom: 2px solid var(--line); margin-bottom: 20px; }
        .tab-button {
            padding: 10px 15px; cursor: pointer; border: none; background: none; 
            font-weight: 600; color: var(--muted); border-bottom: 3px solid transparent; 
            transition: color 0.3s, border-color 0.3s;
        }
        .tab-button:hover { color: var(--text); }
        .tab-button.active { color: var(--brand); border-bottom: 3px solid var(--brand); }
        .tab-content { padding: 10px 0; }

        /* Formulario */
        .form-group{margin-bottom:15px;}
        label{display:block;margin-bottom:5px;font-weight:600;font-size:14px;color:var(--muted);}
        input[type="text"], input[type="email"], input[type="password"] {
            width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:12px;
            font-size:16px;transition:border-color .3s;
        }
        input:focus{outline:none;border-color:var(--brand);}
        .btn{border:0;border-radius:12px;background:var(--brand);color:#fff;padding:12px 18px;font-weight:600;cursor:pointer;width:100%;transition:background .3s;}
        .btn:hover{background:var(--brand-2);}
        
        /* Mensajes */
        #message{margin-top:20px;padding:15px;border-radius:12px;text-align:center;display:block; font-weight: 600;}
        .success{background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;}
        .error{background:#f8d7da;color:#842029;border:1px solid #f5c2c7;}
        .info{background:#cfe2ff;color:#084298;border:1px solid #b6d4fe; padding: 15px; border-radius: 12px;}

        /* Asignaturas Checkbox Grid */
        .asignaturas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            padding: 10px;
            border: 1px solid var(--line);
            border-radius: 12px;
            max-height: 400px;
            overflow-y: auto;
            background: #fafafa;
        }
        .asignatura-item {
            display: flex;
            align-items: center;
            font-size: 15px;
            cursor: pointer;
            padding: 5px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .asignatura-item:hover {
            background: #e9e9f1;
        }
        .asignatura-item input[type="checkbox"] {
            margin-right: 10px;
            width: auto;
        }
        .asignatura-item label {
            margin: 0;
            font-weight: normal;
            color: var(--text);
            cursor: pointer;
        }
        
        /* Tabla de Grupos Asignados */
        .assignment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .assignment-table th, .assignment-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--line);
        }
        .assignment-table th {
            background-color: #f3f4f6;
            color: var(--text);
            font-weight: 600;
        }
        .delete-btn {
            background: #ef4444;
            color: white;
            padding: 6px 10px;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            font-size: 14px;
            transition: background 0.3s;
        }
        .delete-btn:hover {
            background: #dc2626;
        }
        
        .new-assignment-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
            border: 1px solid var(--line);
            padding: 20px;
            border-radius: 12px;
            background: #fcfcfd;
        }
        .new-assignment-form select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            font-size: 16px;
        }
        
        @media (max-width: 600px) {
            .new-assignment-form {
                grid-template-columns: 1fr;
            }
            .assignment-table th, .assignment-table td {
                padding: 8px;
                font-size: 14px;
            }
            /* Ocultar el Curso/Carrera en móvil para simplificar (antes Nivel) */
            .assignment-table th:nth-child(2), 
            .assignment-table td:nth-child(2) {
                display: none; 
            }
            .assignment-table td:last-child {
                 text-align: right;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="docentescreado.php" class="back-link">← Volver al Listado de Docentes</a>

        <h1>Administrar Docente: <?php echo htmlspecialchars($docente['Nombre'] ?? ''); ?> <?php echo htmlspecialchars($docente['Apellido'] ?? ''); ?></h1>
        <p style="text-align: center; color: var(--muted); margin-bottom: 15px;">ID Usuario: <strong><?php echo htmlspecialchars($docente_id); ?></strong> | Correo: <?php echo htmlspecialchars($docente['Correo'] ?? 'N/A'); ?></p>
        
        <?php if ($message_text): ?>
            <div id="message" class="<?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php endif; ?>

        <?php if ($docente): ?>
            <div class="card">
                <!-- Pestañas de Navegación -->
                <div class="tabs">
                    <button class="tab-button <?php echo $current_tab == 'perfil' ? 'active' : ''; ?>" onclick="window.location.href='editar-docente.php?id=<?php echo $docente_id; ?>&tab=perfil'">
                        1. Perfil y Datos
                    </button>
                    <button class="tab-button <?php echo $current_tab == 'asignaturas' ? 'active' : ''; ?>" onclick="window.location.href='editar-docente.php?id=<?php echo $docente_id; ?>&tab=asignaturas'">
                        2. Asignación de Asignaturas
                    </button>
                    <button class="tab-button <?php echo $current_tab == 'grupos' ? 'active' : ''; ?>" onclick="window.location.href='editar-docente.php?id=<?php echo $docente_id; ?>&tab=grupos'">
                        3. Asignación de Grupos
                    </button>
                </div>
                
                <!-- Contenido de las Pestañas -->
                <div class="tab-content">
                    
                    <?php if ($current_tab == 'perfil'): ?>
                        
                        <h2>Datos Personales</h2>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" required placeholder="Nombre" 
                                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? $docente['Nombre'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="apellido">Apellido</label>
                                <input type="text" name="apellido" id="apellido" required placeholder="Apellido" 
                                       value="<?php echo htmlspecialchars($_POST['apellido'] ?? $docente['Apellido'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="documento">Documento / Cédula</label>
                                <input type="text" name="documento" id="documento" required placeholder="Cédula o ID" 
                                       value="<?php echo htmlspecialchars($_POST['documento'] ?? $docente['Documento'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" name="email" id="email" required placeholder="ejemplo@its-p.edu" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? $docente['Correo'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Contraseña (Dejar vacío para no cambiar)</label>
                                <input type="password" name="password" id="password" minlength="6" placeholder="Nueva contraseña (Mínimo 6 caracteres)" 
                                       value="<?php echo htmlspecialchars($password_value); ?>">
                            </div>

                            <button type="submit" class="btn">Guardar Cambios del Perfil</button>
                        </form>

                    <?php elseif ($current_tab == 'asignaturas'): ?>

                        <h2>Asignar Asignaturas Disponibles</h2>
                        <p style="color: var(--muted); margin-bottom: 20px; font-size: 14px;">Seleccione las asignaturas que el docente <strong><?php echo htmlspecialchars($docente['Nombre']); ?></strong> impartirá. Estas asignaciones son un **requisito previo** para asignarle grupos.</p>
                        
                        <?php if (empty($all_asignaturas)): ?>
                            <div class="info" id="message">
                                No hay asignaturas disponibles para asignar. Por favor, cree algunas en el panel de administración.
                            </div>
                        <?php else: ?>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="update_asignaturas">
                                
                                <div class="asignaturas-grid">
                                    <?php foreach ($all_asignaturas as $asignatura): 
                                        $checked = in_array($asignatura['ID_Asignatura'], $asignaturas_asignadas_ids) ? 'checked' : '';
                                    ?>
                                        <div class="asignatura-item">
                                            <input type="checkbox" 
                                                   name="asignaturas[]" 
                                                   id="asignatura_<?php echo $asignatura['ID_Asignatura']; ?>" 
                                                   value="<?php echo $asignatura['ID_Asignatura']; ?>"
                                                   <?php echo $checked; ?>>
                                            <label for="asignatura_<?php echo $asignatura['ID_Asignatura']; ?>">
                                                <?php echo htmlspecialchars($asignatura['Nombre']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="submit" class="btn" style="margin-top: 20px;">Guardar Asignaciones de Asignaturas</button>
                            </form>
                        <?php endif; ?>


                    <?php elseif ($current_tab == 'grupos'): ?>

                        <h2>3. Asignar Grupos y Horarios</h2>
                        <p style="color: var(--muted); font-size: 14px; margin-bottom: 20px;">Asigne al docente a grupos específicos para las asignaturas que tiene permitido impartir (Pestaña 2).</p>
                        
                        <?php if (empty($asignaturas_asignadas_ids)): ?>
                            <div class="info">
                                ⚠️ El docente no tiene asignada **ninguna asignatura**. Por favor, vaya a la Pestaña 2 para asignarle las materias que impartirá.
                            </div>
                        <?php elseif (empty($all_grupos)): ?>
                            <div class="info">
                                ⚠️ No hay **Grupos** disponibles creados en el sistema.
                            </div>
                        <?php else: ?>
                            
                            <!-- Formulario de nueva asignación de grupo -->
                            <form method="POST" action="" class="new-assignment-form">
                                <input type="hidden" name="action" value="add_grupo_asignatura">
                                
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="id_grupo_new">Grupo o Clase</label>
                                    <select name="id_grupo_new" id="id_grupo_new" required>
                                        <option value="">-- Seleccionar Grupo --</option>
                                        <?php foreach ($all_grupos as $grupo): ?>
                                            <option value="<?php echo $grupo['ID_Grupo']; ?>">
                                                <?php echo htmlspecialchars("{$grupo['Nombre']} ({$grupo['Nombre_Curso']})"); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label for="id_asignatura_new">Asignatura a Impartir</label>
                                    <select name="id_asignatura_new" id="id_asignatura_new" required>
                                        <option value="">-- Seleccionar Asignatura (Solo asignadas) --</option>
                                        <?php 
                                        // Filtramos solo las asignaturas que el docente puede impartir
                                        foreach ($asignaturas_asignadas_ids as $asignatura_id):
                                            if (isset($all_asignaturas[$asignatura_id])):
                                                $asignatura = $all_asignaturas[$asignatura_id];
                                        ?>
                                            <option value="<?php echo $asignatura['ID_Asignatura']; ?>">
                                                <?php echo htmlspecialchars($asignatura['Nombre']); ?>
                                            </option>
                                        <?php 
                                            endif;
                                        endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn" style="width: auto; padding: 10px 15px;">
                                    ➕ Asignar Grupo
                                </button>
                            </form>
                            
                            <!-- Listado de Grupos Asignados -->
                            <h3 style="margin-top: 30px; font-size: 1.25rem;">Asignaciones Actuales</h3>
                            
                            <?php if (empty($grupos_asignados_data)): ?>
                                <div class="info" style="margin-top: 15px;">
                                    El docente no tiene asignado ningún grupo/asignatura actualmente.
                                </div>
                            <?php else: ?>
                                <table class="assignment-table">
                                    <thead>
                                        <tr>
                                            <th>Grupo</th>
                                            <th>Curso/Carrera</th>
                                            <th>Asignatura</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($grupos_asignados_data as $data): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($data['Nombre_Grupo']); ?></td>
                                                <td><?php echo htmlspecialchars($data['Nombre_Curso']); ?></td>
                                                <td><?php echo htmlspecialchars($data['Nombre_Asignatura']); ?></td>
                                                <td>
                                                    <form method="POST" action="" style="margin: 0;">
                                                        <input type="hidden" name="action" value="delete_grupo_asignatura">
                                                        <input type="hidden" name="id_grupo_del" value="<?php echo $data['ID_Grupo']; ?>">
                                                        <input type="hidden" name="id_asignatura_del" value="<?php echo $data['ID_Asignatura']; ?>">
                                                        <button type="submit" class="delete-btn" onclick="return confirm('¿Está seguro de eliminar esta asignación?')">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                            
                        <?php endif; ?>
                        
                    <?php endif; ?>

                </div>
            </div>
        <?php else: ?>
             <div class="card error">
                <h2>Error de Carga</h2>
                <p><?php echo $message_text; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Script de soporte JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Ocultar mensaje después de 5 segundos si es un éxito
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
