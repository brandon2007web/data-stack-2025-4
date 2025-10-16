<?php
session_start();
// Eliminar cualquier sesión previa
session_unset();
session_destroy();

// Crear sesión de invitado
session_start();
$_SESSION['usuario'] = 'Invitado';
$_SESSION['rol'] = 8; // rol de invitado

header("Location: ../bienvenido/bienvenido.php");
exit();
?>
