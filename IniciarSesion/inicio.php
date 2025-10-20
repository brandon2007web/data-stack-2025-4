<?php
session_start();//se inicia para poder guardar la informacion del usario mientras navega 
include("../conexion.php");//Se inserta el contenico del archivo "conexion.php", para poder c
// onectarnos con la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //Aca se comprueba que el formulario se halla --
// por metodo POST (para no ejecutar la lógica si alguien entra directo a este archivo desde la URL).
    $usuario     = $_POST['usuario'] ?? null;//se crean 2 variables usuario y contraseña, 
    // que recupera los datos enviados desde el formulario por el usario si no existe tal dato se les asigna null
    $contraseña  = $_POST['contrasena'] ?? null;

    if ($usuario && $contraseña) { //verifica que ambos tengas informacion.
        // Buscar usuario por documento
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE documento = ?");//se crea una variable stmt y eso es igual al 
        // contenido de la variable $conn( contiene el objeto de conexión a la base de datos),que luego se utiliza el metodo prepare para generar la consulta  que nos permitira seleccionar todas las columnas de la tabla "usario", 
        // con la condicion de que  la informacion que puso el usario coincida con la columna "documento", y por ultimo se le agrega el simbolo"?" que es un marcador de posicion
         //Sirve para indicar que en ese lugar se insertará un valor de datos en el momento de la ejecución de la consulta
        $stmt->bind_param("s", $usuario);//se utiliza la variable stmt que es la que contiene la sentencia que qeremos solicitar en nuestra base de datos,bind_param Es el método.
        // que se llama sobre el objeto $stmt para "enlazar" o "vincular" parámetros.Lla "s" indica que el valor de la variable $usuario debe tratarse como una cadena de texto.
        $stmt->execute();// Envía la sentencia SQL al servidor de la base de datos para su ejecución. En este punto, los valores que se vincularon previamente con bind_param() se insertan de forma segura en los marcadores de posición (?) de la consulta. 
        $result = $stmt->get_result();// se crea una variable encargada de  recibir todos los resultados de la consulta guardada en la variable stmt

        if ($result->num_rows === 1) { //aca se encarga deverificar que resultado devuelva una cantidad de columnas iguales a uno
            $user = $result->fetch_assoc();// se cre una variable user  que contiene toda la informacion dentro de la variable  y lo devuelve como un array asociativo


            // Verificar contraseña
            if (password_verify($contraseña, $user['Contrasena'])) {// se crea un if que tiene como condicion comparar el contenido de la variable $contraseña  y  el dato guardado dentro de $user, en la columna Contrasena 

                // Guardar datos en sesión
                $_SESSION['usuario'] = $user['Documento'];//se guarda todo en la variable $_SESSION que es array asociativo de php y se crean los elementos(usuario,rol,nombre) y se les asigna los valores guardados en user. por ejemplo en $_Session se crea un elemento llamado usuario que va a guardar el elemento dentro del array asociativo $user llamado Documento
                $_SESSION['rol']     = $user['ID_Rol'];
                $_SESSION['nombre']  = $user['Nombre'];
                $_SESSION['apellido'] = $user['Apellido'];
                $_SESSION['correo'] = $user['Correo'];
                $_SESSION['usuario_id'] = $user['ID_Usuario']; 



                // Obtener y guardar el nombre del rol en sesión
                $rolStmt = $conn->prepare("SELECT Nombre_Rol FROM rol WHERE ID_Rol = ?");//se crea una variable rolstmt y eso es igual al 
        // contenido de la variable $conn( contiene el objeto de conexión a la base de datos),que luego se utiliza el metodo prepare para generar la consulta  que nos permitira seleccionar todos los Nombre_Rol de la tabla "rol", 
        // con la condicion de que  la informacion que puso el usario coincida con la columna "documento", y por ultimo se le agrega el simbolo"?" que es un marcador de posicion
         //Sirve para indicar que en ese lugar se insertará un valor de datos en el momento de la ejecución de la consulta
                $rolStmt->bind_param("i", $user['ID_Rol']);
                $rolStmt->execute();
                $rolRes = $rolStmt->get_result();
                $rolRow = $rolRes->fetch_assoc();
                $_SESSION['rol_nombre'] = $rolRow['Nombre_Rol'] ?? 'Desconocido';

                // Redirigir según rol
                $rol_nombre = strtolower($_SESSION['rol_nombre']);
                switch ($rol_nombre) {
                    case 'administrador':
                        header("Location: ../bienvenido/bienvenido.php");
                        exit;
                    case 'profesor':
                        header("Location: ../bienvenido/bienvenido.php");
                        exit;
                    default:
                        echo "⚠️ Rol no válido o no encontrado.";
                        exit;
                }

            } else {
                echo "⚠️ Contraseña incorrecta.";
                exit;
            }
        } else {
            echo "⚠️ Usuario no encontrado.";
            exit;
        }
    } else {
        echo "⚠️ Debes ingresar usuario y contraseña.";
        exit;
    }
}
?>
