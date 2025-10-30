   // Manejo del formulario con AJAX
    document.getElementById("formGrupo").addEventListener("submit", function(e) {
      e.preventDefault();

      const curso = document.getElementById("curso").value;
      const grupo = document.getElementById("grupo").value;
      const turno = document.getElementById("turno").value;
      fetch("guardar.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `accion=crearGrupo&curso=${encodeURIComponent(curso)}&grupo=${encodeURIComponent(grupo)}&turno=${encodeURIComponent(turno)}`
      })
      .then(res => res.text())
      .then(data => {
        alert(data);
        location.reload(); // refresca para ver el grupo agregado en la lista
      })
      .catch(err => console.error("Error al guardar grupo:", err));
    });