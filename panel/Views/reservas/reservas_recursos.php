<?php
// =======================
// 1️⃣ Iniciar sesión seguro
// =======================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// =======================
// 2️⃣ Validar usuario logueado
// =======================
if (!isset($_SESSION['usuario_id'])) {
    die("❌ Error: usuario no autenticado.");
}

$usuarioID = $_SESSION['usuario_id'];

// =======================
// 3️⃣ Conectar a la base de datos
// =======================
include(__DIR__ . "/../../../conexion.php");

// =======================
// 4️⃣ Procesar reserva
// =======================
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_Hora'])) {
    $horaID = intval($_POST['ID_Hora']);

    // Verificar si la hora pertenece al docente
    $sqlCheck = "
        SELECT HD.ID_Hora
        FROM docente_grupo DG
        JOIN horario_detalle HD ON DG.ID_Grupo = HD.ID_Grupo AND DG.ID_Asignatura = HD.ID_Asignatura
        WHERE DG.ID_Usuario = ? AND HD.ID_Hora = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($sqlCheck);
    $stmt->bind_param("ii", $usuarioID, $horaID);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        // Registrar reserva
        $sqlInsert = "INSERT INTO reservas (ID_Usuario, ID_Hora, Fecha_Reserva) VALUES (?, ?, NOW())";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("ii", $usuarioID, $horaID);
        if ($stmtInsert->execute()) {
            $mensaje = "✅ ¡Reserva realizada!";
        } else {
            $mensaje = "❌ Error al guardar la reserva.";
        }
        $stmtInsert->close();
    } else {
        $mensaje = "❌ Esa hora no corresponde a tu horario.";
    }

    $stmt->close();
}

// =======================
// 5️⃣ Obtener horario del docente
// =======================
$sql = "
SELECT
    HD.ID_Hora,
    S.Nombre AS Dia,
    H.Nombre AS Bloque_Horario,
    G.Nombre AS Grupo,
    T.Nombre AS Turno,
    A.Nombre AS Asignatura
FROM docente_grupo DG
JOIN horario_detalle HD ON DG.ID_Grupo = HD.ID_Grupo AND DG.ID_Asignatura = HD.ID_Asignatura
JOIN semana S ON HD.ID_Dia = S.ID_Dia
JOIN horas H ON HD.ID_Hora = H.ID_Hora
JOIN grupo G ON DG.ID_Grupo = G.ID_Grupo
JOIN turno T ON G.ID_Turno = T.ID_Turno
JOIN asignatura A ON DG.ID_Asignatura = A.ID_Asignatura
WHERE DG.ID_Usuario = ?
ORDER BY T.ID_Turno, HD.ID_Dia, HD.ID_Hora
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$res = $stmt->get_result();

// Agrupar por Turno → Día
$horario = [];
while ($row = $res->fetch_assoc()) {
    $turno = $row['Turno'] ?? 'Sin Turno';
    $dia = $row['Dia'] ?? 'Sin Día';
    $horario[$turno][$dia][] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Horarios y Reservas</title>
<style>
body { font-family: Arial, sans-serif; }
table { width:100%; border-collapse: collapse; margin-bottom:20px; }
th,td { border:1px solid #ccc; padding:8px; text-align:left; }
th { background:#f2f2f2; }
button { padding:6px 10px; background:#28a745; color:#fff; border:none; cursor:pointer; border-radius:5px; }
button:hover { background:#218838; }
.mensaje { padding:10px; margin-bottom:15px; border-radius:5px; }
.ok { background:#d4edda; color:#155724; }
.err { background:#f8d7da; color:#721c24; }
</style>
</head>
<body>

<h2>📅 Mis Horarios y Reservas</h2>

<?php if($mensaje): ?>
    <div class="mensaje <?= str_contains($mensaje,'✅') ? 'ok' : 'err' ?>">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<?php if(empty($horario)): ?>
    <p>❌ No hay horarios cargados para vos aún.</p>
<?php else: ?>
    <?php foreach($horario as $turno => $dias): ?>
        <h3>Turno: <?= htmlspecialchars($turno) ?></h3>

        <?php foreach($dias as $dia => $bloques): ?>
            <h4><?= htmlspecialchars($dia) ?></h4>
            <table>
                <tr>
                    <th>Hora</th>
                    <th>Grupo</th>
                    <th>Asignatura</th>
                    <th>Acción</th>
                </tr>
                <?php foreach($bloques as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['Bloque_Horario']) ?></td>
                    <td><?= htmlspecialchars($b['Grupo']) ?></td>
                    <td><?= htmlspecialchars($b['Asignatura']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="ID_Hora" value="<?= intval($b['ID_Hora']) ?>">
                            <button>Reservar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
