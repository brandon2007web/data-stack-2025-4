 <?php
        $sql = "SELECT ID_Hora, Nombre, Duracion FROM horas ORDER BY ID_Hora ASC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $inicio = strtotime("07:00");
            $contador_fila = 0;

            while ($row = $result->fetch_assoc()) {
                $idHora     = (int)$row['ID_Hora'];
                $nombre     = htmlspecialchars($row['Nombre']);
                $duracion   = (int)$row['Duracion'];

                $nombre_lower = strtolower($nombre);
                $es_bloque_clase = $nombre_lower != 'recreo' && $nombre_lower != 'pausa';

                $hora_inicio = date('H:i', $inicio);
                $hora_fin    = date('H:i', strtotime("+$duracion minutes", $inicio));

                if (!$es_bloque_clase) {
                    echo "<tr>
                            <td colspan='2' class='num'>{$nombre}</td>
                            <td colspan='5' class='hora'>{$hora_inicio} - {$hora_fin}</td>
                        </tr>";
                } else {
                    $contador_fila++;
                    echo "<tr>
                            <td>{$nombre}</td>
                            <td>{$hora_inicio} - {$hora_fin}</td>
                            <td data-dia='1' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                            <td data-dia='2' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                            <td data-dia='3' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                            <td data-dia='4' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                            <td data-dia='5' data-hora='{$idHora}' data-indice-fila='{$contador_fila}'></td>
                        </tr>";
                }

                $inicio = strtotime("+$duracion minutes", $inicio);
            }
        } else {
            echo "<tr><td colspan='7'>No hay horas cargadas en la BD</td></tr>";
        }
        ?>