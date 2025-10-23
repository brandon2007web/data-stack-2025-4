<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Crear Grupos</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <?php include("../header.php")?>
  <main>
 
  <div class="container">
    <h1>Crear Grupo</h1>

    <!-- Formulario para crear grupo -->
    <form id="formGrupo">
      <label for="curso">Curso:</label>
      <select id="curso" name="curso" required>
        <?php
        include("../../../conexion.php");
        $result = $conn->query("SELECT ID_Curso, Nombre FROM curso");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['ID_Curso'] . "'>" . $row['Nombre'] . "</option>";
            }
        } else {
            echo "<option value=''>No hay cursos cargados</option>";
        }
        ?>
      </select>

      <label for="grupo">Nombre del Grupo:</label>
      <input type="text" id="grupo" name="grupo" placeholder="Ej: Grupo A" required />

      <button type="submit">Guardar Grupo</button>
    </form>

    <!-- Lista de grupos creados -->
    <h2>Grupos Existentes</h2>
    <ul id="listaGrupos">
      <?php
      $result = $conn->query("SELECT g.Nombre as Grupo, c.Nombre as Curso 
                              FROM grupo g 
                              INNER JOIN curso c ON g.ID_Curso = c.ID_Curso");
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<li>" . $row['Grupo'] . " — " . $row['Curso'] . "</li>";
          }
      } else {
          echo "<li>No hay grupos creados</li>";
      }
      ?>
    </ul>
  </div>
  <script src="script.js"></script>
  </main>
</body>
</html>
