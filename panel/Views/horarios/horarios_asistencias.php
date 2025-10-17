<?php
session_start();
// NOTA IMPORTANTE: Asegúrate que esta ruta a "conexion.php" sea correcta desde la ubicación de este archivo.
include __DIR__ . "../../../../conexion.php";

// --------------------------------------------------------------------------------
// CONFIGURACIÓN DE ACCESO Y CONSTANTES
// --------------------------------------------------------------------------------
$usuario_rol = intval($_SESSION['rol'] ?? 0); // 1=Admin, 2=Profesor, 3=Invitado
$usuario_id = intval($_SESSION['usuario_id'] ?? 0);

// Comprobación simple para que el usuario sepa su rol de forma visible para debug.
$rol_nombre = match($usuario_rol) {
    1 => 'Administrador',
    2 => 'Profesor',
    default => 'Invitado'
};

define('HORA_INICIO_CLASE_BASE','07:00:00');

// --------------------------------------------------------------------------------
// FUNCIONES DE RENDERIZADO
// --------------------------------------------------------------------------------
function renderSelectForm($grupos, $grupoSeleccionadoID){
    ob_start(); ?>
<div class="select-form-wrapper">
    <form method="POST" class="select-form">
        <label for="grupo_id">Seleccione el Grupo (Su rol es: <?php echo $GLOBALS['rol_nombre']; ?>):</label>
        <select name="grupo_id" id="grupo_id" required>
            <option value="">-- Elija un Grupo --</option>
            <?php foreach($grupos as $grupo): ?>
                <option value="<?php echo $grupo['ID_Grupo']; ?>"
                    <?php if($grupoSeleccionadoID==$grupo['ID_Grupo']) echo 'selected'; ?>>
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

// --------------------------------------------------------------------------------
// LÓGICA DE DATOS PHP
// --------------------------------------------------------------------------------

// --- Obtener grupos ---
$grupos = [];
if($conn && $res = $conn->query("SELECT ID_Grupo, Nombre FROM grupo ORDER BY Nombre")){
    while($row=$res->fetch_assoc()) $grupos[]=$row;
    $res->free();
}

// --- Selección de grupo ---
$grupoSeleccionadoID = intval($_POST['grupo_id'] ?? 0);

// --- Días y horas ---
$dias=[]; $horas=[];
if($conn){
    $resDias = $conn->query("SELECT ID_Dia, Nombre FROM semana ORDER BY ID_Dia");
    while($row=$resDias->fetch_assoc()) $dias[]=$row;
    $resDias->free();

    $hora_ts = strtotime(HORA_INICIO_CLASE_BASE);
    $resHoras = $conn->query("SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora");
    while($row=$resHoras->fetch_assoc()){
        $row['HoraInicio'] = date('H:i:s',$hora_ts);
        $hora_ts += $row['Duracion']*60;
        $row['HoraFin'] = date('H:i:s',$hora_ts);
        $horas[]=$row;
    }
    $resHoras->free();
}

// --- Horario y asistencias ---
$horarioData=['grupo_nombre'=>'','detalle'=>[]];
$asistencias=[];

if($grupoSeleccionadoID){
    // Horario
    $sql = "SELECT hd.ID_Dia, hd.ID_Hora, s.Nombre AS DiaNombre, h.Nombre AS HoraNombre, a.Nombre AS AsignaturaNombre, g.Nombre AS GrupoNombre
            FROM horario_detalle hd
            JOIN grupo g ON hd.ID_Grupo=g.ID_Grupo
            JOIN semana s ON hd.ID_Dia=s.ID_Dia
            JOIN horas h ON hd.ID_Hora=h.ID_Hora
            JOIN asignatura a ON hd.ID_Asignatura=a.ID_Asignatura
            WHERE hd.ID_Grupo=? ORDER BY hd.ID_Dia, hd.ID_Hora";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$grupoSeleccionadoID);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row=$res->fetch_assoc()){
        $horarioData['detalle'][$row['DiaNombre']][$row['HoraNombre']] = $row['AsignaturaNombre'];
        if($horarioData['grupo_nombre']=='') $horarioData['grupo_nombre']=$row['GrupoNombre'];
    }
    $stmt->close();

    // Asistencias (CORRECCIÓN CRÍTICA: Se añade el filtro por Fecha = CURDATE() para cargar solo la asistencia de hoy)
    $stmt2=$conn->prepare("SELECT ID_Hora, ID_Dia, Justificacion 
                           FROM asistencia 
                           WHERE ID_Grupo=? AND Fecha=CURDATE()"); 
    $stmt2->bind_param("i",$grupoSeleccionadoID);
    $stmt2->execute();
    $res2=$stmt2->get_result();
    while($row=$res2->fetch_assoc()){
        $asistencias[$row['ID_Dia']][$row['ID_Hora']]=$row['Justificacion'];
    }
    $stmt2->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Horario Asistencia</title>
<style>
body{font-family:Segoe UI,Arial,sans-serif;background:#f4f6f9;padding:20px;}
.horario-table{border-collapse:collapse;width:100%;text-align:center;margin:30px auto;box-shadow:0 8px 20px rgba(0,0,0,0.15);border-radius:10px;overflow:hidden;border:1px solid #e0e0e0;}
.horario-table th,.horario-table td{border:1px solid #e0e0e0;padding:12px 8px;height:55px;transition:background-color 0.3s ease;}
.horario-table th{background:#34495e;color:#fff;font-weight:bold;text-transform:uppercase;font-size:13px;}
.hora-header{width:120px;font-weight:bold;background:#e9ecef;color:#495057;position:sticky;left:0;z-index:10;border-right:2px solid #ccc;}
/* El color ausente SÓLO se aplica si el guardado es exitoso */
.ausente{background-color:#e74c3c;color:#fff;font-weight:bold;cursor:pointer;}
.vacio{background-color:#f8f9fa;color:#99aab5;font-style:italic;font-size:13px;}
/* Estilo para las celdas clicables que no son 'Libre' */
.clicable:hover{background-color:#fff3cd; cursor:pointer;} 

.select-form-wrapper{margin-top:25px;text-align:center;}
.select-form{display:inline-block;padding:20px;border:1px solid #ddd;background:#fff;border-radius:8px;width:100%;max-width:450px;text-align:left;box-shadow:0 4px 12px rgba(0,0,0,0.1);display:flex;flex-direction:column;align-items:flex-start;}
.select-form label{display:block;margin-bottom:8px;font-weight:bold;}
.select-form select{width:100%;padding:10px;margin-bottom:15px;border:2px solid #ccc;border-radius:6mm;outline:none;}
.select-form button{padding:12px 20px;background:#2ecc71;color:#fff;border:none;border-radius:6px;cursor:pointer;font-weight:bold;transition:0.3s;width:100%;}
.select-form button:hover{background:#27ae60;}
#modalJustificacion{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background: rgba(0,0,0,0.6);justify-content:center;align-items:center;z-index:9999;}
#modalJustificacion > div{background:#fff;padding:20px;border-radius:8px;width:300px;text-align:center;box-shadow: 0 5px 15px rgba(0,0,0,0.3);}
#modalJustificacion button{margin: 5px; padding: 10px 15px; border-radius: 4px; border: none; cursor: pointer;}
#modalJustificacion #guardarBtn{background-color: #3498db; color: white;}
#modalJustificacion #guardarBtn:hover{background-color: #2980b9;}
</style>
</head>
<body>
<h1>Horario Semanal - <?php echo htmlspecialchars($horarioData['grupo_nombre']); ?></h1>
<?php echo renderSelectForm($grupos,$grupoSeleccionadoID); ?>

<?php if($grupoSeleccionadoID && !empty($horarioData['detalle'])): ?>
<table class="horario-table">
<thead>
<tr><th class="hora-header">Hora / Día</th>
<?php foreach($dias as $dia) echo "<th>".htmlspecialchars($dia['Nombre'])."</th>"; ?>
</tr>
</thead>
<tbody>
<?php foreach($horas as $hora): ?>
<tr>
<th class="hora-header"><?php echo htmlspecialchars($hora['Nombre']); ?><br><small><?php echo substr($hora['HoraInicio'],0,5)."-".substr($hora['HoraFin'],0,5); ?></small></th>
<?php foreach($dias as $dia):
$dia_id=$dia['ID_Dia'];
$asig=$horarioData['detalle'][$dia['Nombre']][$hora['Nombre']]??'Libre';
$just=$asistencias[$dia_id][$hora['ID_Hora']]??null;

// Determina las clases iniciales
$clase = '';
if ($just) {
    $clase = 'ausente'; // Ya tiene asistencia registrada (rojo)
} elseif ($asig == 'Libre') {
    $clase = 'vacio'; // Es hora libre (gris)
} elseif (in_array($usuario_rol,[1,2])) {
    $clase = 'clicable'; // Es una materia clicable para marcar
}

$onclick='';
if(in_array($usuario_rol,[1,2]) && $asig!='Libre') {
    // Si es Admin/Profesor y NO es libre, puede marcar asistencia
    $onclick='onclick="marcarAsistencia(this)"';
} elseif($just) {
    // Si ya tiene justificación, siempre puede verla (cualquier rol)
    $onclick='onclick="verJustificacion(\''.htmlspecialchars($just,ENT_QUOTES).'\')"';
}
?>
<td class="<?php echo $clase;?>" data-dia="<?php echo $dia_id;?>" data-hora="<?php echo $hora['ID_Hora'];?>" data-grupo="<?php echo $grupoSeleccionadoID;?>" data-asignatura="<?php echo htmlspecialchars($asig); ?>" data-justificacion-actual="<?php echo htmlspecialchars($just??'', ENT_QUOTES); ?>" <?php echo $onclick; ?>>
<?php echo htmlspecialchars($asig); ?>
</td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<div id="modalJustificacion">
<div>
<h3 id="modalTitulo"></h3>
<textarea id="justificacion" rows="4" style="width:100%;" placeholder="Ingrese justificación" required></textarea><br><br>
<button id="guardarBtn" onclick="guardarJustificacion()">Guardar</button>
<button onclick="cerrarModal()">Cerrar</button>
</div>
</div>

<script>
let celdaSeleccionada=null;

/**
 * 1. Muestra el modal de justificación al hacer clic en una clase.
 */
function marcarAsistencia(celda){
    celdaSeleccionada=celda;
    
    // 1. Mostrar título correcto
    document.getElementById('modalTitulo').innerText='Justificar Inasistencia en: '+celda.dataset.asignatura;
    
    // 2. Cargar justificación actual si existe
    const justificacionActual = celda.dataset.justificacionActual || '';
    document.getElementById('justificacion').value=justificacionActual;
    
    // 3. Mostrar botón de guardar
    document.getElementById('guardarBtn').style.display='inline-block';
    
    // 4. Mostrar el modal
    document.getElementById('modalJustificacion').style.display='flex';
    console.log('Modal abierto para justificar.');
}

/**
 * 2. Muestra solo la justificación registrada (para cualquier rol).
 */
function verJustificacion(texto){
    document.getElementById('modalTitulo').innerText='Justificación Registrada:';
    document.getElementById('justificacion').value=texto;
    document.getElementById('guardarBtn').style.display='none';
    document.getElementById('modalJustificacion').style.display='flex';
}

/**
 * 3. Cierra el modal, sin afectar el color de la celda.
 */
function cerrarModal(){
    document.getElementById('modalJustificacion').style.display='none';
    // No revertimos el color aquí; solo si falla el guardado.
    celdaSeleccionada=null;
}

/**
 * 4. Envía la justificación al backend (guardar_asistencia.php).
 */
function guardarJustificacion(){
    const just=document.getElementById('justificacion').value.trim();
    if(!just){ 
        console.error('ERROR DE CLIENTE: Debe ingresar justificación.');
        return; 
    }
    
    // Construir datos
    const formData=new FormData();
    formData.append('grupo',celdaSeleccionada.dataset.grupo);
    formData.append('hora',celdaSeleccionada.dataset.hora);
    formData.append('dia',celdaSeleccionada.dataset.dia);
    formData.append('justificacion',just);

    console.log("Enviando datos:", Object.fromEntries(formData));

    // Deshabilitar botón para evitar doble click
    const guardarBtn = document.getElementById('guardarBtn');
    guardarBtn.disabled = true;
    guardarBtn.innerText = 'Guardando...';

    fetch('guardar_asistencia.php',{method:'POST',body:formData})
    .then(res => {
        // Verificar si la petición falló a nivel HTTP (404, 500, etc.)
        if (!res.ok) {
            console.error(`Error de red/servidor. HTTP Status: ${res.status}`);
            throw new Error(`Error de red o archivo no encontrado. Status: ${res.status}.`);
        }
        return res.json();
    })
    .then(data=>{
        guardarBtn.disabled = false;
        guardarBtn.innerText = 'Guardar';
        
        // Procesar la respuesta JSON del servidor
        if(data.ok){ 
            console.log('Guardado correctamente');
            // *** APLICAR COLOR ROJO SOLO SI FUE EXITOSO ***
            celdaSeleccionada.classList.add('ausente');
            
            // Actualizar el atributo data para que se pueda ver la justificación más tarde
            celdaSeleccionada.dataset.justificacionActual = just; 
            
            // Reasignar el onclick para que al tocar muestre la justificación
            celdaSeleccionada.setAttribute('onclick', `verJustificacion('${just.replace(/'/g, "\\'")}')`);
            
        } else { 
            console.error('Error de Guardado (Servidor):', data.error);
            if (data.debug_data) {
                console.warn('Datos de depuración del servidor:', data.debug_data);
            }
            // NO se aplica el color rojo si hay un error
        }
        cerrarModal();
    }).catch(err=>{ 
        guardarBtn.disabled = false;
        guardarBtn.innerText = 'Guardar';
        
        // Capturar errores de conexión o de parseo JSON
        console.error('ERROR CRÍTICO: Error de conexión o fallo al procesar la respuesta:', err.message); 
        // No se aplica el color rojo si hay un error
        cerrarModal(); 
    });
}
</script>
</body>
</html>
