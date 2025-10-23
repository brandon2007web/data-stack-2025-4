<?php
session_start();
require "../../../conexion.php";

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
// 1. OBTENER LISTA DE PISOS
// =========================================================
$pisos = [];
$pisos_result = $conn->query("SELECT ID_Piso, Nombre_Piso FROM pisos ORDER BY ID_Piso ASC");
if ($pisos_result) {
    while ($row = $pisos_result->fetch_assoc()) {
        $pisos[] = $row;
    }
} else {
    $message_text = "❌ Error al cargar la lista de pisos: " . $conn->error;
    $message_type = 'error';
}

// =========================================================
// 2. CREAR AULA (POST)
// =========================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre']) && isset($_POST['id_piso'])) {
    $nombre = trim($_POST['nombre']);
    $id_piso = intval($_POST['id_piso']);
    $tabla_aulas = 'aulas';

    if (empty($nombre) || $id_piso <= 0) {
        $message_text = "❌ Debe especificar el nombre del aula y seleccionar un piso válido.";
        $message_type = 'error';
    } else {
        $sql = "INSERT INTO {$tabla_aulas} (Nombre, ID_Piso) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("si", $nombre, $id_piso);
            if ($stmt->execute()) {
                $_SESSION['message'] = [
                    'text' => "✅ Aula **" . htmlspecialchars($nombre) . "** creada con éxito.",
                    'type' => 'success'
                ];
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
// 3. MOSTRAR AULAS
// =========================================================
$aulas = [];
$sql_select = "SELECT a.ID_Aula, a.Nombre, p.Nombre_Piso 
               FROM aulas a
               JOIN pisos p ON a.ID_Piso = p.ID_Piso
               ORDER BY p.ID_Piso, a.Nombre ASC";
$result = $conn->query($sql_select);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $aulas[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Aulas</title>
    <link rel="stylesheet" href="styles/aulas.css">
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
            <?php if (count($aulas) > 0): ?>
                <?php foreach ($aulas as $row): ?>
                    <div class="aula">
                        <div class="aula-info">
                            <a href="recursos.php?id=<?php echo htmlspecialchars($row['ID_Aula']); ?>" class="nombre">
                                <?php echo htmlspecialchars($row['Nombre']); ?>
                            </a>
                            <span class="piso">Piso: <?php echo htmlspecialchars($row['Nombre_Piso']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay aulas creadas en la base de datos.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="aulas.js"></script>
</body>
</html>
