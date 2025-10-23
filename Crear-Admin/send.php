<?php
include("conexion.php"); // conexión a la base de datos, nos permitiraa utilizar la variable $conn que os permitira hacer consultas SQL

if (isset($_POST['send'])) {//Comprueba que  el formulario fue enviado  atraves del boton name="send", por eso $_Post['send'], y ahi entra al bloque if

    if (!empty($_POST['nombre']) && !empty($_POST['apellido']) && !empty($_POST['usuario']) &&//se comprueba que los campos obligatorios hallan sido completados  enviados
        !empty($_POST['correo']) && !empty($_POST['contraseña']) && !empty($_POST['rol'])) {

        $nombre = mysqli_real_escape_string($conn, trim($_POST['nombre']));//trim() → quita espacios al principio y al final del texto.
        $apellido = mysqli_real_escape_string($conn, trim($_POST['apellido']));//mysqli_real_escape_string($conn, $valor) → protege contra inyección SQL escapando caracteres especiales.
        $usuario = mysqli_real_escape_string($conn, trim($_POST['usuario']));
        $correo = mysqli_real_escape_string($conn, trim($_POST['correo']));
        $rol_nombre = mysqli_real_escape_string($conn, trim($_POST['rol']));
        $password = trim($_POST['contraseña']);
        $codigo_ingresado = isset($_POST['codigoVerificacion']) ? trim($_POST['codigoVerificacion']) : null;//isset($_POST['codigoVerificacion']) ? ... : null → guarda el código ingresado si existe el campo; si no, asigna null.

        
        $query_rol = "SELECT ID_Rol FROM rol WHERE Nombre_Rol = '$rol_nombre' LIMIT 1";//Consulta en la tabla rol el ID del rol (por ejemplo, 1 para “Administrador”, 2 para “Docente”, etc.),LIMIT 1 asegura que devuelva como máximo una fila.
        $resultado_rol = mysqli_query($conn, $query_rol);//mysqli_query() ejecuta la consulta.

        if (!$resultado_rol || mysqli_num_rows($resultado_rol) == 0) {//Si la consulta falla (!$resultado_rol) o no encuentra ningún rol (num_rows == 0), muestra un error y detiene la ejecución con exit().
            echo "<h3 class='error'>Rol inválido</h3>";
            exit();
        }

        $fila_rol = mysqli_fetch_assoc($resultado_rol);//mysqli_fetch_assoc() devuelve la fila como un array asociativo.
        $id_rol = $fila_rol['ID_Rol'];//Se guarda el valor de la columna ID_Rol en $id_rol, que luego se usará al insertar el usuario.

        // Verificar código si es administrador
            if (strtolower($rol_nombre) === 'administrador') {//Convierte $rol_nombre a minúsculas (strtolower) para comparar sin importar mayúsculas/minúsculas,
            $consulta_codigo = "SELECT codigo FROM codigos_admin WHERE codigo = '$codigo_ingresado' LIMIT 1";//Si el rol es “administrador”, exige que el usuario ingrese un código de verificación válido
            $resultado_codigo = mysqli_query($conn, $consulta_codigo);

            if (!$resultado_codigo || mysqli_num_rows($resultado_codigo) == 0) {//Busca ese código en la tabla codigos_admin.
                echo "<h3 class='error'>Código de verificación incorrecto</h3>";//Si no existe, muestra un error y termina la ejecución.
                exit();
            }
        }

        // Hashear la contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);//Encripta (hashea) la contraseña usando el algoritmo recomendado por PHP (actualmente bcrypt o argon2),PASSWORD_DEFAULT elige automáticamente el método más seguro disponible.

        // Insertar usuario con ID_Rol
        $consulta = "INSERT INTO usuario (nombre, apellido, documento, contrasena, correo, ID_Rol) //Inserta un nuevo registro en la tabla usuario con los datos ingresados.
                     VALUES ('$nombre', '$apellido', '$usuario', '$password_hash', '$correo', '$id_rol')";
        $resultado = mysqli_query($conn, $consulta);

        if ($resultado) {//Si la inserción fue exitosa ($resultado verdadero), muestra mensaje de éxito.
            echo "<h3 class='success'>Registro completado</h3>";
        } else {
            echo "<h3 class='error'>Error al registrar: " . mysqli_error($conn) . "</h3>";//Si hubo error, muestra mensaje con detalle del error (mysqli_error($conn)).
        }

    } else {
        echo "<h3 class='error'>Completa todos los campos</h3>";//Si algún campo estaba vacío, muestra aviso de error.
    }
}
?>
