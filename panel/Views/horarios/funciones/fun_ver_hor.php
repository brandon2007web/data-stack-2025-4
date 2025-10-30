<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location:/IniciarSesion/iniciarsesion.php");
    exit();
}
include __DIR__ . "/../../../../conexion.php";

$idRoloUsuario = $_SESSION['rol'] ?? 0;
$tienePermisoEdicion = ($idRoloUsuario == 1 || $idRoloUsuario == 2);

// =========================================
// 1️⃣ Cargar lista de grupos
// =========================================
$grupos = [];
if($res = $conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre")) {
    while($row = $res->fetch_assoc()) $grupos[] = $row;
    $res->free();
}

$grupoSeleccionadoID = $_POST['grupo_id'] ?? null;
$horarioData = null;

// =========================================
// 2️⃣ Si hay grupo seleccionado, cargar horario
// =========================================
if($grupoSeleccionadoID) {
    $grupoSeleccionadoID = intval($grupoSeleccionadoID);
    
    // Obtener el turno del grupo (para saber desde qué hora arrancan las clases)
    $stmt_turno = $conn->prepare("SELECT ID_Turno FROM grupo WHERE ID_Grupo = ?");
    $stmt_turno->bind_param("i", $grupoSeleccionadoID);
    $stmt_turno->execute();
    $res_turno = $stmt_turno->get_result();
    $turno = 1;
    if($row_turno = $res_turno->fetch_assoc()) $turno = intval($row_turno['ID_Turno']);
    $stmt_turno->close();

    // Cargar datos del horario
    $sql = "SELECT hd.ID_Dia, hd.ID_Hora, s.Nombre AS DiaNombre, h.Nombre AS HoraNombre, a.Nombre AS AsignaturaNombre, g.Nombre AS GrupoNombre
            FROM horario_detalle hd
            JOIN grupo g ON hd.ID_Grupo = g.ID_Grupo
            JOIN semana s ON hd.ID_Dia = s.ID_Dia
            JOIN horas h ON hd.ID_Hora = h.ID_Hora
            JOIN asignatura a ON hd.ID_Asignatura = a.ID_Asignatura
            WHERE hd.ID_Grupo = ?
            ORDER BY hd.ID_Dia, hd.ID_Hora";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupoSeleccionadoID);
    $stmt->execute();
    $res = $stmt->get_result();
    $resultados = [];
    $grupoNombre = 'Grupo no encontrado';
    while($row = $res->fetch_assoc()) {
        $resultados[] = $row;
        if($grupoNombre == 'Grupo no encontrado') $grupoNombre = $row['GrupoNombre'];
    }
    $stmt->close();

    $detalle = [];
    foreach($resultados as $row){
        $detalle[$row['DiaNombre']][$row['HoraNombre']] = $row['AsignaturaNombre'];
    }
    $horarioData = ['grupo_nombre'=>$grupoNombre,'detalle'=>$detalle];
}

// =========================================
// 3️⃣ Cargar días
// =========================================
$dias=[];$mapaDias=[];$horas=[];$marcados=[];
if($res = $conn->query("SELECT ID_Dia, Nombre FROM semana ORDER BY ID_Dia")){
    while($row = $res->fetch_assoc()) {
        $dias[] = $row['Nombre']; 
        $mapaDias[$row['Nombre']] = $row['ID_Dia'];
    }
    $res->free();
}

// =========================================
// 4️⃣ Cargar horas con cálculo de rango según turno
// =========================================
$inicio_hora_str = "07:00";
if(isset($turno)){
    if($turno === 2) $inicio_hora_str = "13:35"; // Tarde
    elseif($turno === 3) $inicio_hora_str = "18:00"; // Noche
}

if($res = $conn->query("SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora")){
    $inicio = strtotime($inicio_hora_str);
    while($r = $res->fetch_assoc()) {
        $nombre = $r['Nombre'];
        $duracion = intval($r['Duracion']);
        $hora_inicio = date('H:i', $inicio);
        $hora_fin = date('H:i', strtotime("+$duracion minutes", $inicio));
        $rango = "$hora_inicio - $hora_fin";
        $horas[] = [
            'ID_Hora' => $r['ID_Hora'],
            'Nombre' => "$nombre",
            'Rango' => $rango
        ];
        $inicio = strtotime("+$duracion minutes", $inicio);
    }
    $res->free();
}

// =========================================
// 5️⃣ Cargar marcados
// =========================================
if($grupoSeleccionadoID){
    $stmt = $conn->prepare("SELECT id_dia,id_hora FROM horario_marcado WHERE id_grupo=? AND estado=1");
    $stmt->bind_param("i", $grupoSeleccionadoID);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) $marcados[$row['id_dia']][$row['id_hora']] = true;
    $stmt->close();
}
?>
