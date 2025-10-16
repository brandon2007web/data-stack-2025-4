<?php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión actual

// Redirige a la página de inicio de sesión
header("Location: ../IniciarSesion/iniciarsesion.php");
// O si prefieres al index general:
// header("Location: /index.php");
exit();
?>
