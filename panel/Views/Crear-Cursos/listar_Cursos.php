<?php 
include( __DIR__ .'/php/func_listar-cursos.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listar Cursos</title>
    <link rel="stylesheet" href="styles/listar-cursos.css">
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
        <a href="../Crear-Cursos/crear-Cursos.php" class="back-link">⬅ Volver al Panel Principal</a>
    </div>
</body>
</html>