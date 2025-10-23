<?php
function obtener_mensaje() {
    if (isset($_SESSION['message'])) {
        $mensaje = $_SESSION['message'];
        unset($_SESSION['message']);
        return $mensaje;
    }
    return null;
}
?>
