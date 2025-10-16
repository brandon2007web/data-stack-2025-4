<?php
session_start();
require "../conexion.php"; // Asegúrate de que la ruta sea correcta

$message_text = '';
$message_type = '';

if (isset($_SESSION['message'])) {
    $message_text = $_SESSION['message']['text'];
    $message_type = $_SESSION['message']['type'];
    unset($_SESSION['message']);
}

// Obtener todos los cursos
$cursos = [];
$sql_cursos = "SELECT ID_Curso, Nombre FROM curso ORDER BY Nombre ASC";
$result_cursos = $conn->query($sql_cursos);

if ($result_cursos) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[] = $row;
    }
} else {
    $message_text = "❌ Error al cargar los cursos: " . $conn->error;
    $message_type = 'error';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listar Cursos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f9; color: #333; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 30px; text-align: center; }
        
        .course-list { margin-top: 20px; }
        .course-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px; margin-bottom: 10px; border-radius: 5px;
            background: #e9f5ff; /* Fondo más claro para el item */
            border-left: 5px solid #007bff;
        }
        .course-name { font-weight: bold; font-size: 1.1em; }
        .edit-link {
            padding: 8px 15px; background: #ffc107; color: #343a40; 
            text-decoration: none; border-radius: 4px; transition: background 0.3s;
        }
        .edit-link:hover { background: #e0a800; }
        
        /* Mensajes de Estado */
        .message { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .back-link { display: block; margin-top: 20px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>

<body>
    <div class="container">
        <h1>Gestión de Cursos</h1>

        <?php if ($message_text): ?>
            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message_text; ?>
            </div>
        <?php endif; ?>

        <h2>Cursos Existentes</h2>
        <div class="course-list">
            <?php if (count($cursos) > 0): ?>
                <?php foreach ($cursos as $curso): ?>
                    <div class="course-item">
                        <span class="course-name">
                            <?php echo htmlspecialchars($curso['Nombre']); ?> (ID: <?php echo $curso['ID_Curso']; ?>)
                        </span>
                        <a href="editar-curso-asignaturas.php?id=<?php echo $curso['ID_Curso']; ?>" class="edit-link">
                            Editar Asignaturas
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay cursos registrados. <a href="crear-cursos.php">Crea uno ahora.</a></p>
            <?php endif; ?>
        </div>
        
        <a href="crear-cursos.php" class="back-link" style="margin-top: 30px;">➕ Crear Nuevo Curso</a>
        <a href="aulas.php" class="back-link">⬅ Volver al Panel Principal</a>
    </div>
</body>
</html>