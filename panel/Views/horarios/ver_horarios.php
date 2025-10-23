<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location:/IniciarSesion/iniciarsesion.php");
    exit();
}
include __DIR__ . "../../../../conexion.php";

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
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Horarios</title>
<style>
body{margin:0;font-family:Poppins,sans-serif;background:#f4f6f9;}
.dashboard{display:flex;height:100vh;}
.sidebar{width:250px;background:#2c3e50;color:white;padding:20px;box-sizing:border-box;flex-shrink:0;}
.sidebar h2{margin-top:0;margin-bottom:20px;}
.sidebar select, .sidebar button{width:100%;padding:10px;margin-bottom:15px;border-radius:6px;border:none;}
.sidebar button{background:#27ae60;color:#fff;font-weight:bold;cursor:pointer;transition:0.3s;}
.sidebar button:hover{background:#2ecc71;}
.main{flex-grow:1;overflow:auto;padding:20px;}
.table-wrapper{overflow-x:auto;}
.horario-table{border-collapse:collapse;width:100%;min-width:700px;box-shadow:0 8px 20px rgba(0,0,0,0.1);border-radius:10px;overflow:hidden;}
.horario-table th,.horario-table td{border:1px solid #e0e0e0;padding:12px 8px;text-align:center;white-space:nowrap;}
.horario-table th{background:#34495e;color:#fff;position:sticky;top:0;z-index:2;}
.horario-table th.hora-header{position:sticky;left:0;background:#e9ecef;color:#495057;z-index:3;}
.horario-table td.editable{cursor:pointer;transition:0.3s;}
.horario-table td.editable:hover{background:#d0ebff;}
td.seleccionado{background:#ff4d4d;color:white;font-weight:bold;}
td.vacio{background:#f8f9fa;color:#99aab5;font-style:italic;font-size:13px;}
.tooltip{position:relative;}
.tooltip:hover::after{
    content:attr(data-tooltip);
    position:absolute;top:-30px;left:50%;transform:translateX(-50%);
    background:#333;color:#fff;padding:4px 8px;border-radius:4px;font-size:12px;white-space:nowrap;pointer-events:none;opacity:0.9;
}
button.guardar{margin:20px auto;display:block;padding:12px 20px;background:#27ae60;color:#fff;border:none;border-radius:8px;font-size:16px;cursor:pointer;transition:0.3s;}
button.guardar:hover{background:#2ecc71;transform:scale(1.05);}
</style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Seleccionar Grupo</h2>
        <form method="POST">
            <select name="grupo_id" required>
                <option value="">-- Elija un Grupo --</option>
                <?php foreach($grupos as $g): ?>
                    <option value="<?php echo $g['ID_Grupo']?>" <?php if($grupoSeleccionadoID==$g['ID_Grupo']) echo 'selected';?>><?php echo htmlspecialchars($g['Nombre']);?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Ver Horario</button>
        </form>
    </div>
    <div class="main">
        <?php if($horarioData && !empty($horarioData['detalle'])): ?>
        <h2>Horario: <?php echo htmlspecialchars($horarioData['grupo_nombre']);?></h2>
        <div class="table-wrapper">
        <table class="horario-table">
            <thead>
                <tr><th class="hora-header">Hora / Día</th>
                <?php foreach($dias as $d) echo "<th>".htmlspecialchars($d)."</th>"; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach($horas as $hora): ?>
                <tr>
                <th class="hora-header"><?php echo htmlspecialchars($hora['Nombre']);?></th>
                <?php foreach($dias as $dia):
                    $asignatura=$horarioData['detalle'][$dia][$hora['Nombre']] ?? 'Libre';
                    $clase=($asignatura==='Libre')?'vacio':'';
                    $idDia=$mapaDias[$dia];
                    $idHora=$hora['ID_Hora'];
                    $dataAttributes='';
                    if(isset($marcados[$idDia][$idHora])) $clase.=' seleccionado';
                    if($tienePermisoEdicion && $asignatura!=='Libre'){
                        $clase.=' editable tooltip';
                        $dataAttributes="data-grupo='$grupoSeleccionadoID' data-id-dia='$idDia' data-id-hora='$idHora' data-tooltip='".htmlspecialchars($asignatura)."'";
                    }
                ?>
                <td class="<?php echo $clase;?>" <?php echo $dataAttributes;?>><?php echo htmlspecialchars($asignatura);?></td>
                <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const CAN_EDIT_SCHEDULE = <?php echo $tienePermisoEdicion ? 'true':'false'; ?>;
if(CAN_EDIT_SCHEDULE){
    const horariosMarcados={};
    const celdas=document.querySelectorAll(".horario-table td.editable");
    const btnGuardar=document.createElement("button");
    btnGuardar.textContent="Guardar Horario";
    btnGuardar.classList.add("guardar");
    btnGuardar.style.display="none";
    document.querySelector(".main").appendChild(btnGuardar);

    celdas.forEach(celda=>{
        celda.addEventListener("click",()=>{
            celda.classList.toggle("seleccionado");
            const id_grupo=celda.dataset.grupo;
            const id_dia=celda.dataset.idDia;
            const id_hora=celda.dataset.idHora;
            const estado=celda.classList.contains("seleccionado")?1:0;
            if(!horariosMarcados[id_grupo]) horariosMarcados[id_grupo]={};
            if(!horariosMarcados[id_grupo][id_dia]) horariosMarcados[id_grupo][id_dia]={};
            horariosMarcados[id_grupo][id_dia][id_hora]=estado;
            btnGuardar.style.display="block";
        });
    });

    btnGuardar.addEventListener("click",()=>{
        if(Object.keys(horariosMarcados).length===0){alert("No hay cambios para guardar."); return;}
        fetch("guardar_marcado.php",{
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body:JSON.stringify(horariosMarcados)
        })
        .then(res=>res.text())
        .then(data=>{
            if(data.trim()==="ok"){alert("Horario guardado correctamente!"); location.reload();}
            else alert("Error al guardar: "+data);
        }).catch(err=>alert("Error de conexión: "+err));
    });
}
</script>
</body>
</html>
