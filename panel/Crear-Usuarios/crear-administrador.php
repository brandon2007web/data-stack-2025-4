<?php
session_start();

// =========================================================
// CONFIGURACIÓN FIJA PARA ADMINISTRADOR (ID 1)
// =========================================================
$rol_id = 1;
$rol_nombre = 'Administrador';
$titulo_form = 'Nuevo Administrador';

$message_text = '';
$message_type = '';

// =========================================================
// 0. MANEJO DE MENSAJES DE SESIÓN (PRG Pattern)
// =========================================================
if (isset($_SESSION['success_message'])) {
    $message_text = $_SESSION['success_message'];
    $message_type = 'success';
    unset($_SESSION['success_message']);
}

// =========================================================
// 1. PROCESAMIENTO DE MySQL (Solo en POST)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    include "../../conexion.php"; 

    if (!isset($conn) || $conn->connect_error) {
        $message_type = 'error';
        $connect_error_msg = isset($conn) ? $conn->connect_error : 'El archivo conexion.php no proporcionó el objeto $conn o no existe.';
        $message_text = "❌ Error de conexión a MySQL. Verifique XAMPP y la BD (proyecto_its): " . $connect_error_msg;
    } else {
        $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
        $apellido = $conn->real_escape_string($_POST['apellido'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $documento = $conn->real_escape_string($_POST['cedula'] ?? '');
        $rol_id_post = 1; // Fijo (Administrador)

        // Validación básica
        if (empty($nombre) || empty($email) || empty($documento) || strlen($password) < 6) {
            $message_type = 'error';
            $message_text = '❌ Por favor, complete todos los campos requeridos (incluyendo Documento) y use una contraseña de al menos 6 caracteres.';
        } else {
            // =========================================================
            // 🧩 Verificar si el correo o documento ya existen
            // =========================================================
            $check_sql = "SELECT ID_Usuario FROM usuario WHERE Correo = ? OR Documento = ?";
            if ($check_stmt = $conn->prepare($check_sql)) {
                $check_stmt->bind_param("ss", $email, $documento);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result && $check_result->num_rows > 0) {
                    $message_type = 'error';
                    $message_text = '⚠️ Ya existe un usuario con ese correo o documento.';
                } else {
                    // =========================================================
                    // ✅ Crear nuevo administrador
                    // =========================================================
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO usuario (Nombre, Apellido, Correo, Contrasena, ID_Rol, Documento)
                            VALUES (?, ?, ?, ?, ?, ?)";

                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("ssssis", $nombre, $apellido, $email, $hashed_password, $rol_id_post, $documento);

                        if ($stmt->execute()) {
                            $new_user_id = $stmt->insert_id;
                            $success_message = "✅ Usuario Administrador creado con éxito. ID: **" . $new_user_id . "**";
                            $_SESSION['success_message'] = $success_message;

                            // Redirección (PRG Pattern)
                            header("Location: crear-administrador.php");
                            exit();
                        } else {
                            $message_type = 'error';
                            $message_text = "❌ Error al insertar el registro: " . $stmt->error;
                        }

                        $stmt->close();
                    } else {
                        $message_type = 'error';
                        $message_text = "❌ Error preparando SQL: " . $conn->error;
                    }
                }
                $check_stmt->close();
            } else {
                $message_type = 'error';
                $message_text = "❌ Error preparando verificación de duplicados: " . $conn->error;
            }
        }
        $conn->close();
    }
}

// Mantener contraseña si hubo error
$password_value = ($_SERVER['REQUEST_METHOD'] === 'POST' && $message_type === 'error') ? $_POST['password'] ?? '' : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $titulo_form; ?></title>
    <style>
        :root{--bg:#f7f7fb; --card:#fff; --text:#12141a; --muted:#60646c;--brand:#4f46e5; --brand-2:#4338ca; --line:#e5e7eb; --radius:16px;}
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto}
        .container{max-width:800px;margin:0px auto;padding: 100px 16px 28px;}
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
        header{width: 100%;}
    </style>
</head>
<?php include("../Views/header.php")?>

<body>
    <div class="container">
        
        <h1>Crear <?php echo htmlspecialchars($titulo_form); ?></h1>
        
        <div class="card">
            
            <?php if ($message_text): ?>
                <div id="message" class="<?php echo htmlspecialchars($message_type); ?>">
                    <?php echo $message_text; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="rol_id" id="rol-id" value="1"> 
                <input type="hidden" name="rol_nombre" id="rol-nombre" value="Administrador">
                
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej: Ana" 
                           value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" name="apellido" id="apellido" required placeholder="Ej: Gómez" 
                           value="<?php echo htmlspecialchars($_POST['apellido'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="cedula">Documento / Cédula</label>
                    <input type="text" name="cedula" id="cedula" required placeholder="Cédula" maxlength="10" 
                           value="<?php echo htmlspecialchars($_POST['cedula'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" required placeholder="admin@its-p.edu" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña Temporal</label>
                    <input type="password" name="password" id="password" required minlength="6" placeholder="Mínimo 6 caracteres" 
                           value="<?php echo htmlspecialchars($password_value); ?>">
                </div>

                <button type="submit" class="btn" id="submit-btn">Crear Administrador en MySQL</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const messageDiv = document.getElementById('message');
            if (messageDiv && messageDiv.classList.contains('success')) {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 8000); 
            }
        });
    </script>
</body>
</html>