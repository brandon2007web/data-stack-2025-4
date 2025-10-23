<?php
include(__DIR__ . '/funciones/fun_ver_hor.php');

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Horarios</title>
<link rel="stylesheet" href="styles/ver_hor.css">
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
