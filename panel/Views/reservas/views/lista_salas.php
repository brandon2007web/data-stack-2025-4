<?php
// Obtener todas las salas con su piso (sin duplicados ni conflicto de alias)
$sqlSalas = $conn->query("SELECT 
    a.ID_Aula,
    a.Nombre,
    pi.Nombre_Piso,
    MAX(r.Estado) AS Estado,
    MAX(r.Descripcion_Motivo) AS Descripcion_Motivo
FROM aulas a
LEFT JOIN pisos pi ON a.ID_Piso = pi.ID_Piso
LEFT JOIN reserva r ON a.ID_Aula = r.ID_Aulas
GROUP BY a.ID_Aula, a.Nombre, pi.Nombre_Piso
ORDER BY a.ID_Aula ASC;
");
?>


<div class="container-fluid">
    <h2 class="mb-4">Lista de Salas</h2>

    <!-- Encabezado superior -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

        <div class="search-box mb-2">
            <input type="text" id="buscadorSalas" class="form-control" placeholder="Buscar sala...escriba los espacios tambien">
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>Salas Registradas</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0" id="tablaSalas">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Piso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($s = $sqlSalas->fetch_assoc()): ?>
                        <tr>
                            <td><?= $s['ID_Aula'] ?></td>
                            <td><?= htmlspecialchars($s['Nombre'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($s['Nombre_Piso'] ?? '—') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Buscador -->
<script>
document.getElementById("buscadorSalas").addEventListener("keyup", function() {
    const searchText = this.value.toLowerCase();
    document.querySelectorAll("#tablaSalas tbody tr").forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(searchText) ? "" : "none";
    });
});
</script>
