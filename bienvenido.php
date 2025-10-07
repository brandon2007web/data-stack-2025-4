<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: /IniciarSesion/iniciarsesion.php");
    exit();
}

// Variables de sesión
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$rol_id = $_SESSION['rol'] ?? 0;
$rol_nombre = $_SESSION['rol_nombre'] ?? 'Desconocido';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Portal estilo CREA 2 — Portada + Foro</title>
  <style>
    :root{
      --bg:#f7f7fb; --card:#fff; --text:#12141a; --muted:#60646c;
      --brand:#4f46e5; --brand-2:#4338ca; --line:#e5e7eb; --radius:16px;
    }
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font:16px/1.45 system-ui,-apple-system,Segoe UI,Roboto}
    .container{max-width:1120px;margin:0 auto;padding:0 16px}
    header{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.7);backdrop-filter:saturate(1.2) blur(8px);border-bottom:1px solid var(--line)}
    .header-inner{display:flex;align-items:center;justify-content:space-between;padding:20px 0}
    .brand{display:flex;align-items:center;gap:12px}
    .logo{height:36px;width:36px;border-radius:14px;background:var(--brand);color:#fff;display:grid;place-items:center;font-weight:700}
    .brand h1{margin:0;font-size:16px}
    .brand p{margin:0;color:var(--muted);font-size:12px;margin-top:-2px}
    .user-controls{display:flex;align-items:center;gap:10px}
    .hamburger{cursor:pointer;display:flex;flex-direction:column;gap:4px}
    .hamburger span{display:block;width:24px;height:3px;background:var(--text);border-radius:2px}
    .sidebar{position:fixed;top:0;left:-260px;width:260px;height:100%;background:var(--card);box-shadow:2px 0 8px rgba(0,0,0,.15);padding:20px;transition:left .3s ease;z-index:1000;}
    .sidebar.active{left:0}
    .sidebar h3{margin-top:0}
    .sidebar a{display:block;padding:10px 0;color:var(--text);text-decoration:none}
    .sidebar a:hover{color:var(--brand)}
    .overlay{position:fixed;inset:0;background:rgba(0,0,0,.3);display:none;z-index:900;}
    .overlay.active{display:block}
    .hero{position:relative;height:60vh;min-height:700px;overflow:hidden}
    .hero-bg{position:absolute;inset:0;background-size:cover;background-position:center;transition:background-image .6s ease-in-out}
    .hero-grad{position:absolute;inset:0;background:linear-gradient(to top, rgba(0,0,0,.6), rgba(0,0,0,.05))}
    .hero-content{position:relative;z-index:2;height:100%}
    .hero-card{display:inline-block;background:rgba(255,255,255,.2);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.35);border-radius:18px;padding:16px 18px;margin-bottom:28px}
    .hero-title{color:#fff;margin:0 0 4px;font-size:28px}
    .hero-sub{color:#fff;margin:0;font-size:14px}
    .dots{display:flex;gap:6px;margin-top:8px}
    .dot{height:6px;width:24px;border-radius:999px;background:rgba(255,255,255,.55)}
    .dot.active{background:#fff}
    main{padding:28px 0}
    .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:0 1px 2px rgba(0,0,0,.04);padding:16px}
    textarea{width:100%;border:1px solid var(--line);border-radius:12px;padding:10px;min-height:100px;resize:vertical}
    .btn{border:0;border-radius:12px;background:var(--brand);color:#fff;padding:10px 14px;font-weight:600;cursor:pointer}
    .btn:hover{background:var(--brand-2)}
    .post{padding:14px;border:1px solid var(--line);border-radius:14px}
    .post + .post{margin-top:12px}
    .avatar{height:36px;width:36px;border-radius:999px;background:#4f46e5;color:#fff;display:grid;place-items:center;font-size:14px;font-weight:700}
    .post-head{display:flex;justify-content:space-between;align-items:center;gap:12px}
    .post-meta{display:flex;align-items:center;gap:10px}
    .post-author{font-weight:600}
    .post-time{color:var(--muted);font-size:12px}
    .post-content{white-space:pre-wrap;margin-top:10px}
    footer{margin-top:36px;border-top:1px solid var(--line);background:rgba(255,255,255,.6)}
    .footer-inner{display:flex;justify-content:space-between;align-items:center;padding:14px 0;color:var(--muted);font-size:13px}
  </style>
</head>
<body>
  <!-- Sidebar + Overlay -->
  <?php if ($rol_id == 1): ?>
    <div class="hamburger" id="hamburger" style="position:absolute;top:20px;left:20px;z-index:1100">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <div class="sidebar" id="sidebar">
      <br><br>
      <h3>Menú Admin</h3>
      <a href="horarios/horarios.php">📊 Horarios</a>
      <a href="panel/index.php" style="color:blue;">Panel Admin</a>
      <a href="#">⚙️ Configuración</a>
    </div>
    <div class="overlay" id="overlay"></div>
  <?php endif; ?>

  <!-- Header -->
  <header>
    <div class="container header-inner">
      <div class="brand">
        <div class="logo">D.S</div>
        <div>
          <h1>Data Stack</h1>
          <p>ITSP</p>
        </div>
      </div>
      <div class="user-controls">
        <div class="avatar"><?php echo strtoupper(substr($nombre,0,2)); ?></div>
        <span><?php echo htmlspecialchars($nombre); ?></span>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div id="hero-bg" class="hero-bg"></div>
    <div class="hero-grad"></div>
    <div class="container hero-content" style="display:flex;flex-direction:column;justify-content:end;">
      <div class="hero-card">
        <h2 class="hero-title">Bienvenido/a <?php echo htmlspecialchars($nombre); ?></h2>
        <p class="hero-sub">Recursos, anuncios y espacio de intercambio para la comunidad.</p>
        <div id="dots" class="dots"></div>
      </div>
    </div>
  </section>

  <!-- Foro -->
  <main class="container">
    <h3 style="margin:0;font-size:20px;margin-bottom:10px">Foro</h3>
    <div class="card">
      <form id="post-form">
        <textarea id="contenido" placeholder="Escribe tu mensaje para el foro..."></textarea>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px">
          <p style="color:var(--muted);font-size:12px">Consejo: sé respetuoso y claro. Usa Shift+Enter para salto de línea.</p>
          <button class="btn" type="submit">Publicar</button>
        </div>
      </form>
    </div>
    <section id="lista-posts" style="margin-top:16px"></section>
  </main>

  <footer>
    <div class="container footer-inner">
      <p>© <span id="anio"></span> Portal estilo CREA 2</p>
      <p>Demostración sin backend</p>
    </div>
  </footer>

  <script>
    const IMAGENES = [
      "https://i.postimg.cc/wvLZ6S6N/Chat-GPT-Image-18-ago-2025-20-45-44.png",
      "https://i.postimg.cc/DyDnC3YQ/Chat-GPT-Image-18-ago-2025-19-44-53.png",
      "https://i.postimg.cc/FF6nJr64/Chat-GPT-Image-18-ago-2025-20-26-46.png"
    ];
    const $ = sel => document.querySelector(sel);
    const $$ = (sel,c=document)=>Array.from(c.querySelectorAll(sel));
    const lista = $("#lista-posts");
    const form = $("#post-form");
    const contenido = $("#contenido");
    const heroBg = $("#hero-bg");
    const dots = $("#dots");

    const LS = {
      get(k,f){ try{return JSON.parse(localStorage.getItem(k))??f}catch{ return f} },
      set(k,v){ try{localStorage.setItem(k,JSON.stringify(v))}catch{} }
    };
    let posts = LS.get("foro-posts",[]);
    let idx = 0;

    function renderPosts(){
      if(!posts.length){
        lista.innerHTML = `<div class="card" style="text-align:center;color:var(--muted)">Aún no hay publicaciones.</div>`;
        return;
      }
      lista.innerHTML = posts.map(p => `
        <article class="post">
          <div class="post-head">
            <div class="post-meta">
              <div class="avatar">${p.iniciales}</div>
              <div>
                <div class="post-author">${p.autor}</div>
                <div class="post-time">${new Date(p.fecha).toLocaleString('es-ES')}</div>
              </div>
            </div>
          </div>
          <div class="post-content">${p.texto}</div>
        </article>
      `).join("");
    }

    form.addEventListener("submit", e=>{
      e.preventDefault();
      const txt = contenido.value.trim();
      if(!txt) return;
      const autor = "<?php echo htmlspecialchars($nombre); ?>";
      const iniciales = autor.slice(0,2).toUpperCase();
      const nuevo = {autor,iniciales,texto:txt,fecha:new Date().toISOString()};
      posts = [nuevo, ...posts];
      LS.set("foro-posts", posts);
      contenido.value = "";
      renderPosts();
    });

    function updateHero(){
      heroBg.style.backgroundImage = `url(${IMAGENES[idx]})`;
      $$(".dot").forEach((d,i)=>d.classList.toggle("active", i===idx));
    }

    function renderDots(){
      dots.innerHTML = IMAGENES.map((_,i)=>`<span class="dot${i===idx?' active':''}" data-i="${i}"></span>`).join("");
      dots.addEventListener("click", e=>{
        const el = e.target.closest(".dot"); if(!el) return;
        idx = +el.dataset.i; updateHero();
      });
    }

    setInterval(()=>{ idx=(idx+1)%IMAGENES.length; updateHero(); },5000);

    renderDots();
    updateHero();
    renderPosts();
    $("#anio").textContent = new Date().getFullYear();

    // Sidebar toggle (solo para admin)
    <?php if ($rol_id == 1): ?>
    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");

    if(hamburger){
      hamburger.addEventListener("click", ()=>{
        sidebar.classList.add("active");
        overlay.classList.add("active");
      });

      overlay.addEventListener("click", ()=>{
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
      });
    }
    <?php endif; ?>
  </script>
</body>
</html>
