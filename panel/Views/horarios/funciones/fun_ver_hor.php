<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location:/IniciarSesion/iniciarsesion.php");
    exit();
}
include __DIR__ . "../../../../../conexion.php";

$idRoloUsuario = $_SESSION['rol'] ?? 0;
$tienePermisoEdicion = ($idRoloUsuario == 1 || $idRoloUsuario == 2);

$grupos = [];
if($res=$conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre")) {
    while($row=$res->fetch_assoc()) $grupos[]=$row;
    $res->free();
}

$grupoSeleccionadoID = $_POST['grupo_id'] ?? null;
$horarioData = null;

if($grupoSeleccionadoID) {
    $grupoSeleccionadoID = intval($grupoSeleccionadoID);
    $sql = "SELECT hd.ID_Dia, hd.ID_Hora, s.Nombre AS DiaNombre, h.Nombre AS HoraNombre, a.Nombre AS AsignaturaNombre, g.Nombre AS GrupoNombre
            FROM horario_detalle hd
            JOIN grupo g ON hd.ID_Grupo = g.ID_Grupo
            JOIN semana s ON hd.ID_Dia = s.ID_Dia
            JOIN horas h ON hd.ID_Hora = h.ID_Hora
            JOIN asignatura a ON hd.ID_Asignatura = a.ID_Asignatura
            WHERE hd.ID_Grupo = ?
            ORDER BY hd.ID_Dia, hd.ID_Hora";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$grupoSeleccionadoID);
    $stmt->execute();
    $res = $stmt->get_result();
    $resultados = [];
    $grupoNombre='Grupo no encontrado';
    while($row = $res->fetch_assoc()) {
        $resultados[] = $row;
        if($grupoNombre=='Grupo no encontrado') $grupoNombre=$row['GrupoNombre'];
    }
    $stmt->close();

    $detalle = [];
    foreach($resultados as $row){
        $detalle[$row['DiaNombre']][$row['HoraNombre']] = $row['AsignaturaNombre'];
    }
    $horarioData = ['grupo_nombre'=>$grupoNombre,'detalle'=>$detalle];
}

// Cargar días y horas
$dias=[];$mapaDias=[];$horas=[];$marcados=[];
if($res=$conn->query("SELECT ID_Dia, Nombre FROM semana ORDER BY ID_Dia")){
    while($row=$res->fetch_assoc()) {$dias[]=$row['Nombre']; $mapaDias[$row['Nombre']]=$row['ID_Dia'];}
    $res->free();
}
if($res=$conn->query("SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora")){
    while($row=$res->fetch_assoc()) $horas[]=$row;
    $res->free();
}

// Horarios marcados
if($grupoSeleccionadoID){
    $stmt=$conn->prepare("SELECT id_dia,id_hora FROM horario_marcado WHERE id_grupo=? AND estado=1");
    $stmt->bind_param("i",$grupoSeleccionadoID);
    $stmt->execute();
    $res=$stmt->get_result();
    while($row=$res->fetch_assoc()) $marcados[$row['id_dia']][$row['id_hora']]=true;
    $stmt->close();
}
?>