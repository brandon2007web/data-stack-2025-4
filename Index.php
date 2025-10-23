<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenidos a OrganizaT</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f4f6fc;
    color: #1e1e2f;
  }

  header {
    background: #4f46e5;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  header .logo a {
    text-decoration: none;
    font-weight: 600;
    font-size: 1.5rem;
    color: #fff;
  }

  nav ul {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
  }

  nav ul li a {
    text-decoration: none;
    color: #fff;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 8px;
    transition: background 0.3s;
  }

  nav ul li a:hover {
    background: rgba(255,255,255,0.2);
  }

  .container {
    max-width: 800px;
    margin: 120px auto 50px;
    text-align: center;
    padding: 0 20px;
  }

  h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: #4f46e5;
  }

  p {
    font-size: 1.1rem;
    color: #6c6f85;
    line-height: 1.6;
  }

  .btn-group {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
  }

  .btn {
    text-decoration: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s;
  }

  .btn-entrar {
    background: #27ae60;
    color: #fff;
  }

  .btn-entrar:hover {
    background: #2ecc71;
    transform: scale(1.05);
  }

  .btn-login {
    background: #4f46e5;
    color: #fff;
  }

  .btn-login:hover {
    background: #4338ca;
    transform: scale(1.05);
  }

  @media(max-width:600px){
    .btn-group {
      flex-direction: column;
    }
  }
</style>
</head>
<body>

<header>
  <div class="logo">
    <a href="index.php">OrganizaT</a>
  </div>
  <nav>
    <ul>
      <li><a href="IniciarSesion/invitado.php">Entrar</a></li>
      <li><a href="IniciarSesion/iniciarsesion.php">Iniciar Sesión</a></li>
    </ul>
  </nav>
</header>

<div class="container">
  <h1>Bienvenido a OrganizaT</h1>
  <p>
    Este sitio web tiene como objetivo ayudar a administrar la Institución ITSP de manera más rápida y segura, evitando errores y redundancias. 
    Si eres un alumno o visitante, toca "Entrar". Si eres profesor o administrador, haz clic en "Iniciar Sesión".
  </p>
  <div class="btn-group">
    <a href="IniciarSesion/invitado.php" class="btn btn-entrar">Entrar</a>
    <a href="IniciarSesion/iniciarsesion.php" class="btn btn-login">Iniciar Sesión</a>
  </div>
</div>

</body>
</html>
