<?php
// Consultas para el Dashboard

// Total aulas
$sqlAulas = $conn->query("SELECT COUNT(*) AS total FROM aulas");
$totalAulas = $sqlAulas->fetch_assoc()['total'];

// Total reservas
$sqlReservas = $conn->query("SELECT COUNT(*) AS total FROM reserva");
$totalReservas = $sqlReservas->fetch_assoc()['total'];

// Reservas activas HOY
$hoy = date('Y-m-d');
$sqlHoy = $conn->query("SELECT COUNT(*) AS total FROM reserva WHERE DATE(Fecha_Inicio) = '$hoy'");
$reservasHoy = $sqlHoy->fetch_assoc()['total'];

// Últimas 5 reservas
$sqlUltimas = $conn->query("
    SELECT r.*, u.nombre AS usuario, a.Nombre AS aula
    FROM reserva r
    INNER JOIN usuario u ON r.ID_Usuario = u.ID_Usuario
    INNER JOIN aulas a ON r.ID_Aulas = a.ID_Aula
    ORDER BY r.ID_Reserva DESC LIMIT 5
");
?>

<div class="container-fluid">

    <h2 class="mb-4">Dashboard</h2>

    <!-- CARDS -->
    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3><?php echo $totalAulas; ?></h3>
                    <p class="text-muted">Total de Salas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3><?php echo $totalReservas; ?></h3>
                    <p class="text-muted">Total Reservas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3><?php echo $reservasHoy; ?></h3>
                    <p class="text-muted">Reservas Hoy</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h3>Admin</h3>
                    <p class="text-muted">Rol del Usuario</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Últimas reservas -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <strong>Últimas Reservas</strong>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Sala</th>
                        <th>Usuario</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($r = $sqlUltimas->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $r['ID_Reserva']; ?></td>
                        <td><?php echo $r['aula']; ?></td>
                        <td><?php echo $r['usuario']; ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($r['Fecha_Inicio'])); ?></td>
                        <td><?php echo date("d/m/Y H:i", strtotime($r['Fecha_Fin'])); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $r['Estado'] == 'Activa' ? 'success' : 'secondary'; ?>">
                                <?php echo $r['Estado']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
