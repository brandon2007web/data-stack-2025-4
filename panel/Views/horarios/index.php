
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Horario Semanal</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="container">
    <h1>HORARIO SEMANAL</h1>

    <table id="horario">
      <thead>
        <tr>
          <th></th>
          <th>Hora</th>
          <th>Lunes</th>
          <th>Martes</th>
          <th>Miércoles</th>
          <th>Jueves</th>
          <th>Viernes</th>
        </tr>
      </thead>
      <tbody>
        <!-- 1ª -->
        <tr>
          <td rowspan="2" class="num">1ª</td>
          <td class="hora">07:00</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">07:45</td></tr>

        <!-- 2ª -->
        <tr>
          <td rowspan="2" class="num">2ª</td>
          <td class="hora">07:50</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">08:35</td></tr>

        <!-- 3ª -->
        <tr>
          <td rowspan="2" class="num">3ª</td>
          <td class="hora">08:40</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">09:25</td></tr>

        <!-- 4ª -->
        <tr>
          <td rowspan="2" class="num">4ª</td>
          <td class="hora">09:30</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">10:15</td></tr>

        <!-- 5ª -->
        <tr>
          <td rowspan="2" class="num">5ª</td>
          <td class="hora">10:20</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">11:05</td></tr>

        <!-- 6ª -->
        <tr>
          <td rowspan="2" class="num">6ª</td>
          <td class="hora">11:10</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">11:55</td></tr>

        <!-- 7ª -->
        <tr>
          <td rowspan="2" class="num">7ª</td>
          <td class="hora">12:00</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">12:45</td></tr>

        <!-- 8ª -->
        <tr>
          <td rowspan="2" class="num">8ª</td>
          <td class="hora">12:50</td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
          <td rowspan="2"></td>
        </tr>
        <tr><td class="hora">13:35</td></tr>
      </tbody>
    </table>

    <button id="guardarBtn" style="display:none;">Guardar Horario</button>


    <!-- Contenedor de los dos formularios -->
    <div class="formularios">
      
      <!-- Formulario Materias -->
      <div class="formulario">
        <h2>Agregar Materia</h2>
        <label for="dia">Día:</label>
        <select id="dia">
          <option value="2">Lunes</option>
          <option value="3">Martes</option>
          <option value="4">Miércoles</option>
          <option value="5">Jueves</option>
          <option value="6">Viernes</option>
        </select>

        <label for="bloque">Bloque (1ª–8ª):</label>
        <select id="bloque">
          <option value="1">1ª</option>
          <option value="2">2ª</option>
          <option value="3">3ª</option>
          <option value="4">4ª</option>
          <option value="5">5ª</option>
          <option value="6">6ª</option>
          <option value="7">7ª</option>
          <option value="8">8ª</option>
        </select>

        <label for="materia">Materia:</label>
        <input type="text" id="materia" placeholder="Ej.: Programación" />

        <button id="agregarBtn">Agregar</button>
      </div>

      <!-- Formulario Cursos -->
         <div class="formulario">
  <h2>Agregar Curso</h2>
  <label for="curso">Curso:</label>
  <select id="curso" name="curso">
    <?php
      include("conexion.php"); // tu conexión
      $result = $conn->query("SELECT ID_Curso, Nombre FROM curso");
      while ($row = $result->fetch_assoc()) {
          echo "<option value='" . $row['ID_Curso'] . "'>" . $row['Nombre'] . "</option>";
      }
    ?>
  </select>
        <label for="grupo">Grupo:</label>
        <input type="text" id="grupo" placeholder="Ej.: Grupo A" />

        <button id="agregarCursoBtn">Agregar Curso</button>

        <!-- Lista de cursos -->
        <ul id="listaCursos"></ul>
      </div>
    </div>
   
  </div>

  <script src="lol.js"></script>
</body>
</html>
