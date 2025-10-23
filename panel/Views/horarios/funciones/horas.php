<?php   $resHoras = $conn->query("SELECT ID_Hora, Nombre FROM horas ORDER BY ID_Hora ASC");
            while ($h = $resHoras->fetch_assoc()) {
                $nombre_h = strtolower($h['Nombre']);
                if ($nombre_h != 'recreo' && $nombre_h != 'pausa') {
                    echo "<option value='{$h['ID_Hora']}'>" . htmlspecialchars($h['Nombre']) . "</option>";
                }
            }?>