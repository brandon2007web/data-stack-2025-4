<?php
function obtener_mensaje() {
    if (isset($_SESSION['message'])) {
        $mensaje = $_SESSION['message'];
        unset($_SESSION['message']);
        return $mensaje;
    }
    return ['text' => '', 'type' => ''];
}

function set_mensaje($texto, $tipo = 'success') {
    $_SESSION['message'] = [
        'text' => $texto,
        'type' => $tipo
    ];
}
?>
