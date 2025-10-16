document.addEventListener("DOMContentLoaded", function () {
    const agregarBtn = document.getElementById("agregarBtn");
    const guardarBtn = document.getElementById("guardarBtn");
    const tabla = document.getElementById("horario");
    const grupoSelect = document.getElementById("grupo");
    const materiaSelect = document.getElementById("materia");
    const diaSelect = document.getElementById("dia");
    const bloqueSelect = document.getElementById("bloque");
    const nombreInput = document.getElementById("nombreHorario");

    // --- Cargar materias según grupo ---
    grupoSelect.addEventListener("change", function () {
        const grupoId = this.value;
        materiaSelect.innerHTML = "<option value=''>Cargando...</option>";

        if (!grupoId) {
            materiaSelect.innerHTML = "<option value=''>Seleccione un grupo primero</option>";
            return;
        }

        // 🚨 Asegúrate de que esta URL sea la correcta para tu archivo get_materias.php
        fetch("get_materias.php?id_grupo=" + encodeURIComponent(grupoId))
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
    });

    // --- Doble clic para borrar celda ---
    tabla.querySelectorAll("td[data-dia][data-hora]").forEach(celda => {
        celda.addEventListener("dblclick", function () {
            if (this.hasAttribute("data-materia")) {
                if (confirm("¿Eliminar esta materia de la celda?")) {
                    this.textContent = "";
                    this.style.backgroundColor = "";
                    this.removeAttribute("data-materia");
                    this.removeAttribute("data-grupo");
                    this.removeAttribute("data-dia"); // Limpiar todos los atributos data
                    this.removeAttribute("data-hora");
                    guardarBtn.style.display = "inline-block";
                }
            }
        });
    });

    // --- Agregar materia a la celda ---
    agregarBtn.addEventListener("click", function () {
        const grupo = grupoSelect.value;
        const materia = materiaSelect.value;
        const materiaNombre = materiaSelect.options[materiaSelect.selectedIndex]?.text || "";
        const dia = Number(diaSelect.value);
        const idHoraBD = Number(bloqueSelect.value);
        const indiceFila = bloqueSelect.selectedIndex + 1; // Ajuste si la tabla tiene cabecera y el select no

        if (!grupo || !materia || !dia || !idHoraBD) {
            alert("Complete todos los campos de selección (grupo, día, hora, materia).");
            return;
        }

        // Usa el atributo data-hora directamente para la selección de la celda
        const celda = tabla.querySelector(`td[data-dia="${dia}"][data-hora="${idHoraBD}"]`);
        
        if (!celda) {
            alert(`No se encontró la celda correspondiente para Día: ${dia} y Hora ID: ${idHoraBD}.`);
            return;
        }

        // 🚨 DEBUG: Muestra que la materia se intenta colocar
        console.log(`Intentando colocar materia: Grupo=${grupo}, Materia=${materia}, Día=${dia}, Hora=${idHoraBD}`);


        celda.textContent = materiaNombre;
        celda.setAttribute("data-materia", materia);
        celda.setAttribute("data-grupo", grupo);
        // Aunque ya están, las reescribimos para consistencia
        celda.setAttribute("data-dia", dia);
        celda.setAttribute("data-hora", idHoraBD);
        celda.style.backgroundColor = "#d0f0d0";

        // 🚨 DEBUG: Muestra los atributos de la celda colocada
        console.log("Materia colocada en celda:", {
            celda: celda, 
            attrs: {
                dia: celda.dataset.dia, 
                hora: celda.dataset.hora, 
                materia: celda.dataset.materia, 
                grupo: celda.dataset.grupo
            }
        });

        guardarBtn.style.display = "inline-block";
    });

    // --- Guardar horario ---
    guardarBtn.addEventListener("click", () => {
        const nombreHorario = nombreInput.value.trim();
        let grupoID = parseInt(grupoSelect.value); // Usamos 'let' para poder reasignar

        // Construye el array de datos del horario
        const datosHorario = [];
        tabla.querySelectorAll("td[data-materia][data-dia][data-hora][data-grupo]").forEach(celda => {
            datosHorario.push({
                dia: parseInt(celda.dataset.dia),
                hora: parseInt(celda.dataset.hora),
                materia: parseInt(celda.dataset.materia),
                grupo: parseInt(celda.dataset.grupo) // Ya tienes el grupo correcto de la celda
            });
        });

        // 💡 RESPALDO: Si grupoID es 0 (o NaN) Y hay datos, usa el grupo del primer bloque como respaldo.
        // Esto soluciona el error de grupoID:0 si el select no estaba seleccionado al guardar.
        if ((!grupoID || isNaN(grupoID)) && datosHorario.length > 0) {
            grupoID = datosHorario[0].grupo;
        }

        // 🚨 Validación Estricta: Si alguno falta, la alerta previene el envío
        if (!nombreHorario) {
            alert("Debe ingresar un nombre para el horario.");
            return;
        }
        if (!grupoID || grupoID === 0 || isNaN(grupoID)) {
             alert("El grupo principal del horario es inválido. Asegúrese de que haya un grupo seleccionado.");
            return;
        }
        if (datosHorario.length === 0) {
            alert("Debe agregar al menos un bloque de materia al horario antes de guardar.");
            return;
        }

        // 🚨 DEBUG FINAL: Muestra los datos que se enviarán
        console.log("------------------------------------------");
        console.log(`ENVIANDO | Nombre: ${nombreHorario}, GrupoID: ${grupoID}, Items: ${datosHorario.length}`);
        console.log("Datos Horario a enviar:", datosHorario);
        console.log("------------------------------------------");

        // Enviar al servidor
        fetch("guardarhorario.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                nombre: nombreHorario,
                grupoID: grupoID,
                datos: datosHorario
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert(data.message);
                // Opcional: recargar o limpiar el formulario
                // location.reload(); 
            } else {
                alert("⚠️ Error al guardar: " + data.message);
                // Muestra la respuesta del servidor con los valores que recibió
                console.error("Respuesta de Error del Servidor:", data); 
            }
        })
        .catch(err => {
            console.error("Error en fetch (red o servidor no responde):", err);
            alert("Ocurrió un error de red o el servidor no respondió.");
        });
    });
});