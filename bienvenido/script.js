const IMAGENES = [
  "https://i.postimg.cc/wvLZ6S6N/Chat-GPT-Image-18-ago-2025-20-45-44.png",
  "https://i.postimg.cc/DyDnC3YQ/Chat-GPT-Image-18-ago-2025-19-44-53.png",
  "https://i.postimg.cc/FF6nJr64/Chat-GPT-Image-18-ago-2025-20-26-46.png"
];

const $ = sel => document.querySelector(sel);
const $$ = (sel, c = document) => Array.from(c.querySelectorAll(sel));

const lista = $("#lista-posts");
const form = $("#post-form");
const contenido = $("#contenido");
const heroBg = $("#hero-bg");
const dots = $("#dots");

const LS = {
  get(k, f) {
    try {
      return JSON.parse(localStorage.getItem(k)) ?? f;
    } catch {
      return f;
    }
  },
  set(k, v) {
    try {
      localStorage.setItem(k, JSON.stringify(v));
    } catch {}
  }
};

let posts = LS.get("foro-posts", []);
let idx = 0;

// Renderiza las publicaciones
function renderPosts() {
  if (!posts.length) {
    lista.innerHTML = `
      <div class="card" style="text-align:center;color:var(--muted)">
        Aún no hay publicaciones.
      </div>`;
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

// Manejo del formulario
form.addEventListener("submit", e => {
  e.preventDefault();
  const txt = contenido.value.trim();
  if (!txt) return;

  const autor = autorNombre || "Anónimo";
  const iniciales = autor.slice(0, 2).toUpperCase();
  const nuevo = {
    autor,
    iniciales,
    texto: txt,
    fecha: new Date().toISOString()
  };

  posts = [nuevo, ...posts];
  LS.set("foro-posts", posts);
  contenido.value = "";
  renderPosts();
});

// Actualiza el fondo del hero
function updateHero() {
  heroBg.style.backgroundImage = `url(${IMAGENES[idx]})`;
  $$(".dot").forEach((d, i) => d.classList.toggle("active", i === idx));
}

// Renderiza los puntos de navegación
function renderDots() {
  dots.innerHTML = IMAGENES.map((_, i) =>
    `<span class="dot${i === idx ? ' active' : ''}" data-i="${i}"></span>`
  ).join("");

  dots.addEventListener("click", e => {
    const el = e.target.closest(".dot");
    if (!el) return;
    idx = +el.dataset.i;
    updateHero();
  });
}

// Rotación automática de imágenes del hero
setInterval(() => {
  idx = (idx + 1) % IMAGENES.length;
  updateHero();
}, 5000);

// Inicialización
renderDots();
updateHero();
renderPosts();
$("#anio").textContent = new Date().getFullYear();

// Sidebar toggle solo si es admin
if (typeof esAdmin !== "undefined" && esAdmin) {
  const hamburger = document.getElementById("hamburger");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("overlay");

  if (hamburger && sidebar && overlay) {
    hamburger.addEventListener("click", () => {
      sidebar.classList.add("active");
      overlay.classList.add("active");
    });

    overlay.addEventListener("click", () => {
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    });
  }
}
