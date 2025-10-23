<?php
// 1. SOLUCIÓN: Iniciar la sesión para cargar la variable $_SESSION['rol'].
session_start(); 
if (!isset($_SESSION['usuario'])) {
    header("Location: /IniciarSesion/iniciarsesion.php");
    exit();
}

include __DIR__ . "../../../../conexion.php";
// La constante HORA_INICIO_CLASE_BASE y su uso han sido eliminados.

// Obtiene ID_Rol de la sesión. Si no hay sesión o no hay rol, se usa 0.
$idRoloUsuario = $_SESSION['rol'] ?? 0;

// Definimos si el usuario tiene permiso de edición (ID_Rol 1 o 2)
$tienePermisoEdicion = ($idRoloUsuario == 1 || $idRoloUsuario == 2);

function renderSelectForm($grupos, $grupoSeleccionadoID) {
    ob_start();
?>
<div class="select-form-wrapper">
    <form method="POST" class="select-form">
        <label for="grupo_id">Seleccione el Grupo:</label>
        <select name="grupo_id" id="grupo_id" required>
            <option value="">-- Elija un Grupo --</option>
            <?php foreach ($grupos as $grupo): ?>
                <option value="<?php echo htmlspecialchars($grupo['ID_Grupo']); ?>" <?php if ($grupoSeleccionadoID == $grupo['ID_Grupo']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($grupo['Nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Ver Horario</button>
    </form>
</div>
<?php
    return ob_get_clean();
}

function obtenerHorarioPorGrupo($conn, $idGrupo) {
    if (!$conn) return ['grupo_nombre'=>'Error de Conexión','detalle'=>[]];
    $sql = "SELECT hd.ID_Dia, hd.ID_Hora, s.Nombre AS DiaNombre, h.Nombre AS HoraNombre, a.Nombre AS AsignaturaNombre, g.Nombre AS GrupoNombre
            FROM horario_detalle hd
            JOIN grupo g ON hd.ID_Grupo = g.ID_Grupo
            JOIN semana s ON hd.ID_Dia = s.ID_Dia
            JOIN horas h ON hd.ID_Hora = h.ID_Hora
            JOIN asignatura a ON hd.ID_Asignatura = a.ID_Asignatura
            WHERE hd.ID_Grupo = ? ORDER BY hd.ID_Dia, hd.ID_Hora";
    $resultados=[];
    $grupoNombre='Grupo No Encontrado';
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("i",$idGrupo);
    $stmt->execute();
    $res=$stmt->get_result();
    while($row=$res->fetch_assoc()){
        $resultados[]=$row;
        if($grupoNombre=='Grupo No Encontrado') $grupoNombre=$row['GrupoNombre'];
    }
    $stmt->close();
    $horarioEstructurado=[];
    foreach($resultados as $row){
        $horarioEstructurado[$row['DiaNombre']][$row['HoraNombre']]=$row['AsignaturaNombre'];
    }
    return ['grupo_nombre'=>$grupoNombre,'detalle'=>$horarioEstructurado];
}

$grupos=[];
if(isset($conn) && $res=$conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre")){while($row=$res->fetch_assoc()) $grupos[]=$row;$res->free();}

$horarioData=null;
$grupoSeleccionadoID=null;
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['grupo_id'])){
    $grupoSeleccionadoID=intval($_POST['grupo_id']);
    if($grupoSeleccionadoID>0) $horarioData=obtenerHorarioPorGrupo($conn,$grupoSeleccionadoID);
}

$dias=[];$mapaDias=[];$horas=[];$marcados=[];
if(isset($conn)){
    if($res=$conn->query("SELECT ID_Dia, Nombre FROM semana ORDER BY ID_Dia")){
        while($row=$res->fetch_assoc()){$dias[]=$row['Nombre'];$mapaDias[$row['Nombre']]=$row['ID_Dia'];}
        $res->free();
    }
    
    // Obtención de horas simplificada (se asume que 'Nombre' de la tabla 'horas' es el rango de tiempo)
    if($res=$conn->query("SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora")){
        while($row=$res->fetch_assoc()){
            $horas[]=$row;
        }
        $res->free();
    }
    
    // 2. SOLUCIÓN: SIEMPRE obtenemos los horarios marcados si se seleccionó un grupo, independientemente del rol.
    if($grupoSeleccionadoID > 0){ 
        $stmt=$conn->prepare("SELECT id_dia,id_hora FROM horario_marcado WHERE id_grupo=? AND estado=1");
        $stmt->bind_param("i",$grupoSeleccionadoID);
        $stmt->execute();
        $res=$stmt->get_result();
        // $marcados ahora contiene los horarios rojos para todos los roles
        while($row=$res->fetch_assoc()) $marcados[$row['id_dia']][$row['id_hora']]=true;
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Horario por Grupo</title>
<style>
body{font-family:Arial;background:#f4f6f9;margin:0;padding:20px;color:#333;display:flex;justify-content:center;}
.container{width:100%;max-width:1100px;text-align:center;}
.horario-table{border-collapse:collapse;width:100%;margin:30px auto;font-size:14px;box-shadow:0 8px 20px rgba(0,0,0,0.15);border-radius:10px;overflow:hidden;border:1px solid #e0e0e0;}
.horario-table th,.horario-table td{border:1px solid #e0e0e0;padding:12px 8px;vertical-align:middle;height:55px;}
.horario-table th{background:#34495e;color:#fff;font-weight:bold;text-transform:uppercase;font-size:13px;}
.horario-table td{background:#fff;transition:background 0.3s;max-width:200px;min-width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:500;}
/* Hacemos que sea editable SOLO si tiene la clase 'editable', que solo se aplica a roles 1 y 2 */
.horario-table td.editable{cursor:pointer;} 
.horario-table td.editable:hover{background:#e9f5ff;}
.hora-header{width:120px;font-weight:bold;background:#e9ecef;color:#495057;position:sticky;left:0;z-index:10;border-right:2px solid #ccc;line-height:1.3;}
/* small tag para el tiempo eliminada */
.vacio{background:#f8f9fa;color:#99aab5;font-style:italic;font-size:13px;cursor:default;}
td.seleccionado{background:#ff4d4d !important;color:white;font-weight:bold;}
.select-form-wrapper{margin-top:25px;text-align:center;}
.select-form{display:inline-block;padding:20px;border:1px solid #ddd;background:#fff;border-radius:8px;width:100%;max-width:450px;text-align:left;}
.select-form select{width:100%;padding:10px;margin-bottom:15px;border:2px solid #ccc;border-radius:6px;outline:none;}
.select-form button{padding:12px 20px;background:#2ecc71;color:#fff;border:none;border-radius:6px;font-size:16px;font-weight:bold;width:100%;cursor:pointer;}
.select-form button:hover{background:#27ae60;}
.debug-info{padding:10px;margin-bottom:15px;background:#ffe0b2;border:1px solid #ff9800;border-radius:5px;text-align:center;}
</style>
</head>
<body>
<div class="container">

<h1>Horario Semanal por Grupo</h1>
<?php echo renderSelectForm($grupos,$grupoSeleccionadoID); ?>

<?php if($horarioData && !empty($horarioData['detalle'])): ?>
<h2>Horario del Grupo: <?php echo htmlspecialchars($horarioData['grupo_nombre']); ?></h2>
<table class="horario-table">
<thead>
<tr><th class="hora-header">Hora / Día</th><?php foreach($dias as $d){echo "<th>".htmlspecialchars($d)."</th>";} ?></tr>
</thead>
<tbody>
<?php foreach($horas as $hora): ?>
<tr>
<th class="hora-header">
    <?php echo htmlspecialchars($hora['Nombre']); ?>
</th>
<?php foreach($dias as $dia):
    $asignatura=$horarioData['detalle'][$dia][$hora['Nombre']] ?? 'Libre';
    $clase=($asignatura==='Libre')?'vacio':'';
    $idDia=$mapaDias[$dia];
    $idHora=$hora['ID_Hora'];

    $dataAttributes = '';
    
    // 3. SOLUCIÓN: Aplicar color rojo (seleccionado) para TODOS los usuarios.
    if(isset($marcados[$idDia][$idHora])) {
        $clase.=' seleccionado';
    }

    // Lógica de Permiso y asignación de clases de edición (SOLO para roles 1 y 2).
    if($tienePermisoEdicion && $asignatura !== 'Libre'){
        $dataAttributes = "data-grupo=\"$grupoSeleccionadoID\" data-id-dia=\"$idDia\" data-id-hora=\"$idHora\"";
        $clase .= ' editable'; // Clase CLAVE que activa el click y el hover para editores
    }
?>
<td class="<?php echo $clase; ?>" <?php echo $dataAttributes; ?>>
<?php echo htmlspecialchars($asignatura); ?>
</td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
</div>

<script>
// --- PASAMOS EL ESTADO DE PERMISO DE PHP A JAVASCRIPT ---
const CAN_EDIT_SCHEDULE = <?php echo $tienePermisoEdicion ? 'true' : 'false'; ?>;

document.addEventListener("DOMContentLoaded", function() {
    // Si el usuario tiene permiso (ID_Rolo 1 o 2), activamos la funcionalidad
    if (CAN_EDIT_SCHEDULE) {
        // Seleccionamos SOLO las celdas que PHP marcó como editables
        const celdas = document.querySelectorAll(".horario-table td.editable");
        const horariosMarcados = {};

        // 1. Lógica de Marcado (Toggle 'seleccionado') - Solo funciona en celdas 'editable'
        celdas.forEach(celda => {
            celda.addEventListener("click", function() {
                this.classList.toggle("seleccionado");
                const id_grupo = this.dataset.grupo;
                const id_dia   = this.dataset.idDia;
                const id_hora  = this.dataset.idHora;
                const estado   = this.classList.contains("seleccionado") ? 1 : 0;

                // Guardar cambio en objeto temporal
                if (!horariosMarcados[id_grupo]) horariosMarcados[id_grupo] = {};
                if (!horariosMarcados[id_grupo][id_dia]) horariosMarcados[id_grupo][id_dia] = {};
                horariosMarcados[id_grupo][id_dia][id_hora] = estado;
            });
        });

        // 2. Crear y Configurar Botón de Guardar
        const btnGuardar = document.createElement("button");
        btnGuardar.textContent = "Guardar Horario";
        btnGuardar.style.margin = "20px auto";
        btnGuardar.style.padding = "12px 20px";
        btnGuardar.style.background = "#2ecc71";
        btnGuardar.style.color = "#fff";
        btnGuardar.style.border = "none";
        btnGuardar.style.borderRadius = "6px";
        btnGuardar.style.fontSize = "16px";
        btnGuardar.style.cursor = "pointer";
        btnGuardar.style.display = "block";

        document.querySelector(".container").appendChild(btnGuardar);

        btnGuardar.addEventListener("click", function() {
            if (Object.keys(horariosMarcados).length === 0) {
                alert("No hay cambios para guardar.");
                return;
            }

            fetch("guardar_marcado.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(horariosMarcados)
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "ok") {
                    alert("Horario guardado correctamente!");
                    // Vaciar cambios guardados y recargar para ver los nuevos marcados
                    for (let g in horariosMarcados) delete horariosMarcados[g];
                    location.reload(); 
                } else {
                    alert("Error al guardar: " + data);
                }
            })
            .catch(err => alert("Error al conectar con el servidor: " + err));
        });
    }
});
</script>
</body>
</html>
