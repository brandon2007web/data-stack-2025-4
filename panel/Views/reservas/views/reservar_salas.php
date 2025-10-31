<?php
include(__DIR__ . "/../../../../conexion.php");
?>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<link rel="stylesheet" href="public/css/reservas.css">

<div class="container-reserva">
  <h2 class="titulo-reserva">📅 Reservar Sala</h2>

  <div class="layout-reserva">
    <!-- CALENDARIO -->
    <div class="calendar-container">
      <div id="calendar"></div>
    </div>

    <!-- FORMULARIO -->
    <div class="form-container">
      <div class="card-form">
        <div class="card-header"><strong>Nueva Reserva</strong></div>
        <div class="card-body">
          <form id="formReserva">
            <div class="form-group">
              <label for="sala">Seleccionar Sala</label>
              <select class="form-select" id="sala" name="sala" required>
                <option value="">Seleccione una sala</option>
                <?php
                $salas = $conn->query("SELECT ID_Aula, Nombre FROM aulas ORDER BY Nombre ASC");
                while($s = $salas->fetch_assoc()):
                ?>
                  <option value="<?= $s['ID_Aula'] ?>"><?= htmlspecialchars($s['Nombre']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="nombre">Reserva a Nombre de:</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
              <label for="fecha">Fecha:</label>
              <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>

            <div class="form-group">
              <label>Hora de inicio:</label>
              <select class="form-select" id="hora_inicio" name="hora_inicio" required>
                <option value="">Seleccione hora</option>
                <?php
                $hora = strtotime("07:00");
                while ($hora <= strtotime("24:00")) {
                  echo '<option value="' . date("H:i", $hora) . '">' . date("H:i", $hora) . '</option>';
                  $hora = strtotime("+50 minutes", $hora);
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label>Hora de fin:</label>
              <select class="form-select" id="hora_fin" name="hora_fin" required>
                <option value="">Seleccione hora</option>
                <?php
                $hora = strtotime("07:50");
                while ($hora <= strtotime("24:50")) {
                  echo '<option value="' . date("H:i", $hora) . '">' . date("H:i", $hora) . '</option>';
                  $hora = strtotime("+50 minutes", $hora);
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="motivo">Observaciones:</label>
              <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
            </div>

            <div class="buttons">
              <button type="submit" class="btn btn-primary">Guardar</button>
              <button type="reset" class="btn btn-secondary">Resetear</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('formReserva');
  const calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    height: 'auto',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: {
      url: 'views/get.reservas.php',
      method: 'GET',
      failure: function() {
        alert('Error al cargar reservas!');
      }
    },
    eventColor: '#28a745',
    eventDisplay: 'block',
  });

  calendar.render();

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch('views/guardar_reserva.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        alert('✅ Reserva guardada con éxito');
        form.reset();
        calendar.refetchEvents();
      } else {
        alert('❌ Error al guardar la reserva');
        console.error(data.error);
      }
    })
    .catch(err => {
      console.error(err);
      alert('⚠️ Error al conectar con el servidor.');
    });
  });
});
</script>
