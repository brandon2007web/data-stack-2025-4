document.addEventListener("DOMContentLoaded", function () {
    const agregarBtn = document.getElementById("agregarBtn");
    const guardarBtn = document.getElementById("guardarBtn");
    const tabla = document.getElementById("horario");
    const materiaSelect = document.getElementById("materia");
    const diaSelect = document.getElementById("dia");
    const bloqueSelect = document.getElementById("bloque");
    const nombreInput = document.getElementById("nombreHorario");

    // --- Cargar materias del grupo seleccionado (ya guardado en sesión) ---
    if (typeof GRUPO_ID !== "undefined" && GRUPO_ID > 0) {
        materiaSelect.innerHTML = "<option value=''>Cargando materias...</option>";

        fetch("get_materias.php?id_grupo=" + encodeURIComponent(GRUPO_ID))
            .then(res => res.json())
            .then(data => {
                const materias = data.ok ? data.materias : [];
                materiaSelect.innerHTML = "<option value=''>-- Seleccione --</option>";

                if (materias.length === 0) {
                    materiaSelect.innerHTML = "<option value=''>No hay materias disponibles</option>";
                    return;
                }

                materias.forEach(m => {
                    const option = document.createElement("option");
                    option.value = m.id;
                    option.textContent = m.nombre;
                    materiaSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error("Error al traer materias:", err);
                materiaSelect.innerHTML = "<option value=''>Error al cargar materias</option>";
            });
    } else {
        alert("No se ha seleccionado un grupo. Volviendo a selección...");
        window.location.href = "seleccionar_grupo.php";
    }

    // --- Doble clic para borrar celda ---
    tabla.querySelectorAll("td[data-dia][data-hora]").forEach(celda => {
        celda.addEventListener("dblclick", function () {
            if (this.hasAttribute("data-materia")) {
                if (confirm("¿Eliminar esta materia de la celda?")) {
                    this.textContent = "";
                    this.style.backgroundColor = "";
                    this.removeAttribute("data-materia");
                    guardarBtn.style.display = "inline-block";
                }
            }
        });
    });

    // --- Agregar materia a la celda ---
    agregarBtn.addEventListener("click", function () {
        const materia = materiaSelect.value;
        const materiaNombre = materiaSelect.options[materiaSelect.selectedIndex]?.text || "";
        const dia = Number(diaSelect.value);
        const idHoraBD = Number(bloqueSelect.value);

        if (!materia || !dia || !idHoraBD) {
            alert("Complete todos los campos (día, hora, materia).");
            return;
        }

        const celda = tabla.querySelector(`td[data-dia="${dia}"][data-hora="${idHoraBD}"]`);
        if (!celda) {
            alert(`No se encontró la celda para Día ${dia} y Hora ${idHoraBD}.`);
            return;
        }

        celda.textContent = materiaNombre;
        celda.setAttribute("data-materia", materia);
        celda.setAttribute("data-grupo", GRUPO_ID);
        celda.style.backgroundColor = "#d0f0d0";

        guardarBtn.style.display = "inline-block";
    });

    // --- Guardar horario ---
    guardarBtn.addEventListener("click", () => {
        const nombreHorario = nombreInput.value.trim();

        const datosHorario = [];
        tabla.querySelectorAll("td[data-materia][data-dia][data-hora]").forEach(celda => {
            datosHorario.push({
                dia: parseInt(celda.dataset.dia),
                hora: parseInt(celda.dataset.hora),
                materia: parseInt(celda.dataset.materia),
                grupo: GRUPO_ID
            });
        });

        if (!nombreHorario) {
            alert("Debe ingresar un nombre para el horario.");
            return;
        }
        if (datosHorario.length === 0) {
            alert("Debe agregar al menos un bloque antes de guardar.");
            return;
        }

        console.log("Enviando datos:", { nombreHorario, grupo: GRUPO_ID, datosHorario });

        fetch("guardarhorario.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                nombre: nombreHorario,
                grupoID: GRUPO_ID,
                datos: datosHorario
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("✅ " + data.message);
            } else {
                alert("⚠️ Error: " + data.message);
            }
        })
        .catch(err => {
            console.error("Error en fetch:", err);
            alert("Error de red o servidor no disponible.");
        });
    });
});
