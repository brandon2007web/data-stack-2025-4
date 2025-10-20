<?php
// Cargar comprobaciones y datos del usuario
require('comprobacion.php');

// Variables que pasarán a la vista
$nombre = $nombre ?? 'Invitado';
$rol_id = $rol_id ?? 0;

// Incluir la vista
include('vista_portal.php');
