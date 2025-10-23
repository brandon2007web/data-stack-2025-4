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
// CAMBIO 1: OBTENER LISTA DE PISOS
// =========================================================
$pisos = [];
$pisos_result = $conn->query("SELECT ID_Piso, Nombre_Piso FROM pisos ORDER BY ID_Piso ASC");
if ($pisos_result) {
    while ($row = $pisos_result->fetch_assoc()) {
        $pisos[] = $row;
    }
} else {
    // Si la tabla pisos no existe o hay un error, mostrar mensaje.
    $message_text = "❌ Error al cargar la lista de pisos: " . $conn->error;
    $message_type = 'error';
}

// =========================================================
// 2. PROCESAMIENTO: CREAR AULA (POST)
// =========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre']) && isset($_POST['id_piso'])) {
    
    // 2.1. Obtener y sanitizar datos
    $nombre = trim($_POST['nombre']);
    $id_piso = intval($_POST['id_piso']); // Convertir a entero por seguridad
    
    // Asumimos que la tabla de aulas se llama 'aulas' y tiene la columna 'ID_Piso' (según tu requerimiento)
    // El script original usa 'espacio', lo mantendré así para la tabla de aulas, pero agregaré el ID_Piso.
    // Si tu tabla es 'aulas', cambia 'espacio' por 'aulas'.
    $tabla_aulas = 'aulas'; // <-- Cambia esto si tu tabla de aulas tiene otro nombre

    // 2.2. Validación básica
    if (empty($nombre) || $id_piso <= 0) {
        $message_text = "❌ Error: Debe especificar el nombre del aula y seleccionar un piso válido.";
        $message_type = 'error';
    } else {
        // 2.3. Sentencia preparada para SEGURIDAD
        // MODIFICACIÓN CLAVE: Añadir ID_Piso al INSERT
        $sql = "INSERT INTO {$tabla_aulas} (Nombre, ID_Piso) VALUES (?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Vincular los parámetros (s = string, i = integer)
            $stmt->bind_param("si", $nombre, $id_piso);

            // 2.4. Ejecutar la consulta
            if ($stmt->execute()) {
                // 2.5. Configurar mensaje de éxito en la sesión
                $_SESSION['message'] = [
                    'text' => "✅ Aula **" . htmlspecialchars($nombre) . "** creada con éxito.",
                    'type' => 'success'
                ];
                
                // 2.6. Implementar Patrón PRG (Redirección)
                header("Location: aulas.php"); 
                exit();
            } else {
                // Error de violación de unicidad (aula ya existe) o error general
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
// 3. OBTENER DATOS: MOSTRAR AULAS (GET)
// =========================================================
$aulas = [];
$tabla_aulas = 'aulas'; // <-- Cambia esto si tu tabla de aulas tiene otro nombre

// MODIFICACIÓN CLAVE: Usar JOIN para obtener el Nombre del Piso
$sql_select = "SELECT 
                    a.ID_Aula, 
                    a.Nombre, 
                    p.Nombre_Piso 
               FROM {$tabla_aulas} a
               JOIN pisos p ON a.ID_Piso = p.ID_Piso
               ORDER BY p.ID_Piso, a.Nombre ASC";
               
$result = $conn->query($sql_select);

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
            position: fixed; top: 0; left: 0; 
            width: 100%; background: white; border-bottom: 1px solid #ddd; z-index: 1000; 
        }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        
        /* Estilos de formulario */
        .form-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-group { display: flex; gap: 10px; align-items: center; }
        input[type="text"], select {
            padding: 10px; border: 1px solid #ccc; border-radius: 4px; 
            flex-grow: 1; /* Permite que los campos se expandan */
        }
        select { flex-basis: 35%; } /* Asignar un tamaño fijo al select del piso */
        input[type="text"] { flex-basis: 60%; } /* Asignar un tamaño fijo al input del nombre */
        button[type="submit"] {
            padding: 10px 15px; background: #007bff; color: white; border: none; 
            border-radius: 4px; cursor: pointer; transition: background 0.3s;
            flex-shrink: 0; /* Evita que el botón se encoja */
        }
        button[type="submit"]:hover { background: #0056b3; }

        /* Estilos de listado */
        .aula-list { margin-top: 20px; }
        .aula {
            background: white; padding: 15px; margin: 10px 0; border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-left: 5px solid #007bff;
            display: flex; justify-content: space-between; align-items: center;
        }
        .aula-info { display: flex; flex-direction: column; }
        .aula-info .nombre { text-decoration: none; color: #007bff; font-weight: bold; font-size: 1.1em; }
        .aula-info .piso { font-size: 0.9em; color: #555; margin-top: 3px; }
        .aula-list a:hover { text-decoration: underline; }
        
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
                <div class="form-group">
                    <input type="text" name="nombre" placeholder="Nombre del Aula (Ej: Aula 301)" required maxlength="100">
                    
                    <select name="id_piso" required>
                        <option value="">Seleccione el Piso</option>
                        <?php foreach ($pisos as $piso): ?>
                            <option value="<?php echo htmlspecialchars($piso['ID_Piso']); ?>">
                                <?php echo htmlspecialchars($piso['Nombre_Piso']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Crear Aula</button>
                </div>
            </form>
        </div>
        
        <div class="aula-list">
            <h2>Listado de Aulas Disponibles (<?php echo count($aulas); ?>)</h2>
            <?php
            if (count($aulas) > 0) {
                foreach ($aulas as $row) {
                    // CAMBIO 3: Mostrar el nombre del piso
                    // Usaremos 'ID_Aula' en lugar de 'ID_Espacio' (ajusta si es necesario)
                    echo "<div class='aula'>";
                    echo "  <div class='aula-info'>";
                    echo "      <a href='recursos.php?id=" . htmlspecialchars($row['ID_Aula']) . "' class='nombre'>" . htmlspecialchars($row['Nombre']) . "</a>";
                    echo "      <span class='piso'>Piso: " . htmlspecialchars($row['Nombre_Piso']) . "</span>";
                    echo "  </div>";
                    // Aquí podrías agregar botones de editar/eliminar si los tuvieras
                    echo "</div>";
                }
            } else {
                echo "<p>No hay aulas creadas en la base de datos o hubo un error al cargar.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>