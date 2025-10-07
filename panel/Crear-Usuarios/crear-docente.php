<?php
session_start();

// Obtener el rol de la URL (1 para Admin, 2 para Docente)
$rol_id = intval($_GET['rol'] ?? 2);
$rol_nombre = ($rol_id == 1) ? 'Administrador' : 'Docente';
$titulo_form = ($rol_id == 1) ? 'Nuevo Administrador' : 'Nuevo Docente';

$message_text = '';
$message_type = '';

// =========================================================
// 0. MANEJO DE MENSAJES DE SESIÓN (PRG Pattern)
// =========================================================
// Si hay un mensaje de éxito después de la redirección (PRG), lo mostramos y lo limpiamos.
if (isset($_SESSION['success_message'])) {
    $message_text = $_SESSION['success_message'];
    $message_type = 'success';
    unset($_SESSION['success_message']);
}

// =========================================================
// 1. PROCESAMIENTO DE MySQL (Solo en POST)
// =========================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 2. Cargar el archivo de conexión externo.
    // Se asume que este archivo (conexion.php) establece la variable $conn.
    include "../../conexion.php"; 

    // Verificar la conexión usando el objeto $conn proporcionado por conexion.php
    if (!isset($conn) || $conn->connect_error) {
        $message_type = 'error';
        $connect_error_msg = isset($conn) ? $conn->connect_error : 'El archivo conexion.php no proporcionó el objeto $conn o no existe.';
        $message_text = "❌ Error de conexión a MySQL. Verifique XAMPP y la BD (proyecto_its).: " . $connect_error_msg;
    } else {
        // 3. Obtener y sanitizar datos del formulario
        $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
        $apellido = $conn->real_escape_string($_POST['apellido'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $documento = $conn->real_escape_string($_POST['cedula'] ?? '');
        $rol_id_post = intval($_POST['rol_id'] ?? 2);

        // Validación básica (deberías agregar más validaciones de email, etc.)
        if (empty($nombre) || empty($email) || strlen($password) < 6) {
             $message_type = 'error';
             $message_text = '❌ Por favor, complete todos los campos requeridos y use una contraseña de al menos 6 caracteres.';
        } else {
            // =========================================================================
            // ✅ SEGURIDAD: Usar Hashing de Contraseñas (password_hash)
            // =========================================================================
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 4. Preparar la consulta SQL
            // Nombres de columnas usados: Nombre, Apellido, Correo, Contrasena, ID_Rol, Documento
            $sql = "INSERT INTO usuario (Nombre, Apellido, Correo, Contrasena, ID_Rol, Documento) VALUES (?, ?, ?, ?, ?, ?)";
            
            // Preparar statement
            if ($stmt = $conn->prepare($sql)) {
                // Vincular variables a la sentencia preparada como parámetros (s = string, i = integer)
                // ORDEN: Nombre(s), Apellido(s), Correo(s), Contrasena/Hash(s), ID_Rol(i), Documento(s)
                $stmt->bind_param("ssssis", $nombre, $apellido, $email, $hashed_password, $rol_id_post, $documento);

                // 5. Ejecutar la consulta
                if ($stmt->execute()) {
                    $new_user_id = $stmt->insert_id;
                    
                    // 6. Configurar mensaje de éxito en la sesión
                    $success_message = "✅ Usuario " . htmlspecialchars($rol_nombre) . " creado con éxito. ID de MySQL: **" . $new_user_id . "** (Contraseña hasheada)";
                    $_SESSION['success_message'] = $success_message;
                    
                    // =========================================================================
                    // 7. IMPLEMENTAR PATRÓN PRG: REDIRECCIÓN
                    // Esto evita el mensaje de reenvío de formulario del navegador (F5/Refrescar).
                    // =========================================================================
                    $redirect_url = "crear-docente.php"; // Usamos $rol_id_post
                    header("Location: $redirect_url");
                    exit(); // Detener la ejecución del script
                    
                } else {
                    $message_type = 'error';
                    $message_text = "❌ Error al insertar el registro. " . $stmt->error;
                }

                // Cerrar statement
                $stmt->close();
            } else {
                $message_type = 'error';
                $message_text = "❌ Error de preparación de SQL: " . $conn->error;
            }
        }
        // Cerrar conexión
        // Esto solo se ejecuta si hubo un error de validación o inserción antes del redirect
        $conn->close();
    }
}
// La contraseña solo se rellena si hay un error en POST (para que el usuario no la pierda)
$password_value = ($_SERVER['REQUEST_METHOD'] === 'POST' && $message_type === 'error') ? $_POST['password'] ?? '' : '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root{--bg:#f7f7fb; --card:#fff; --text:#12141a; --muted:#60646c;--brand:#4f46e5; --brand-2:#4338ca; --line:#e5e7eb; --radius:16px;}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto}
        .container{max-width:600px;margin:0 auto;padding:28px 16px}
        .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 4px 6px rgba(0,0,0,.05);padding:30px;}
        h1{color:var(--text);margin-bottom:20px;text-align:center;}
        .form-group{margin-bottom:15px;}
        label{display:block;margin-bottom:5px;font-weight:600;font-size:14px;color:var(--muted);}
        input[type="text"], input[type="email"], input[type="password"] {
            width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:12px;
            font-size:16px;transition:border-color .3s;
        }
        input:focus{outline:none;border-color:var(--brand);}
        .btn{border:0;border-radius:12px;background:var(--brand);color:#fff;padding:12px 18px;font-weight:600;cursor:pointer;width:100%;transition:background .3s;}
        .btn:hover{background:var(--brand-2);}
        .btn:disabled{background:#ccc;cursor:not-allowed;} 
        .back-link{display:block;margin-bottom:20px;text-align:center;color:var(--brand);text-decoration:none;font-weight:500;}
        #message{margin-top:20px;padding:10px;border-radius:12px;text-align:center;display:block;}
        .success{background:#d1e7dd;color:#0f5132;border:1px solid #badbcc;}
        .error{background:#f8d7da;color:#842029;border:1px solid #f5c2c7;}
        .security-warning{color:#f00;font-weight:600;text-align:center;margin-bottom:15px;padding:10px;border:1px solid #f88;border-radius:12px;}
        /* Estilo específico para el botón en el mensaje de éxito */
        .success-action-btn {
            background: var(--brand); 
            color: #fff; 
            padding: 10px 20px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
            display: inline-block; 
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success-action-btn:hover {
            background: var(--brand-2);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../crear-usuarios.php" class="back-link">← Volver a Selección de Rol</a>
        
        <h1>Crear <?php echo htmlspecialchars($titulo_form); ?></h1>
        
        <div class="card">
            
            <?php if ($message_text): ?>
                <div id="message" class="<?php echo htmlspecialchars($message_type); ?>">
                    <?php echo $message_text; ?>
                    
                    <?php 
                    // Mostrar el botón solo si fue un éxito Y el rol creado fue Docente (rol_id=2)
                    if ($message_type == 'success' && $rol_id == 2): 
                    ?>
                        <div style="margin-top: 20px;">
                            <a href="docentescreado.php" class="success-action-btn">
                                Ver Docentes Creados y Administrar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- El formulario ahora envía datos a sí mismo (crear-usuario-form.php) -->
            <form method="POST" action="">
                <input type="hidden" name="rol_id" id="rol-id" value="<?php echo htmlspecialchars($rol_id); ?>">
                <input type="hidden" name="rol_nombre" id="rol-nombre" value="<?php echo htmlspecialchars($rol_nombre); ?>">

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej: Juan" 
                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" id="apellido" required placeholder="Ej: Pérez" 
                           value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>">
                </div>
                
                <!-- Campo de Cédula/Documento solo para Docente (rol_id = 2) -->
                <?php if ($rol_id == 2): ?>
                <div class="form-group">
                    <label for="cedula">Documento / Cédula</label>
                    <input type="text" name="cedula" id="cedula" required placeholder="Cédula o ID del docente" maxlength="10" 
                           value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" required placeholder="ejemplo@its-p.edu" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña Temporal</label>
                    <input type="password" name="password" id="password" required minlength="6" placeholder="Mínimo 6 caracteres" 
                           value="<?php echo htmlspecialchars($password_value); ?>">
                </div>

                <button type="submit" class="btn" id="submit-btn">Crear Usuario en MySQL</button>
            </form>
        </div>
    </div>

    <!-- Script de soporte JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Ocultar mensaje después de 5 segundos si es un éxito
            const messageDiv = document.getElementById('message');
            if (messageDiv && messageDiv.classList.contains('success')) {
                setTimeout(() => {
                    // Solo ocultamos el texto, dejamos el botón visible si existe
                    const textNode = messageDiv.firstChild; 
                    if (textNode.nodeType === 3) {
                         textNode.style.display = 'none';
                    }
                }, 8000); // 8 segundos para que dé tiempo a ver el mensaje
            }
        });
    </script>
</body>
</html>
