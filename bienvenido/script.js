
  document.getElementById('anio').textContent = new Date().getFullYear();

  document.addEventListener("DOMContentLoaded", () => {

    // HERO
    const IMAGENES = [
      "https://i.postimg.cc/wvLZ6S6N/Chat-GPT-Image-18-ago-2025-20-45-44.png",
      "https://i.postimg.cc/DyDnC3YQ/Chat-GPT-Image-18-ago-2025-19-44-53.png",
      "https://i.postimg.cc/FF6nJr64/Chat-GPT-Image-18-ago-2025-20-26-46.png"
    ];
    const heroBg = document.getElementById("hero-bg");
    const dotsContainer = document.getElementById("dots");
    let idx = 0;

    function updateHero(){
      heroBg.style.backgroundImage = `url(${IMAGENES[idx]})`;
      dotsContainer.querySelectorAll(".dot").forEach((d,i)=>d.classList.toggle("active",i===idx));
    }
    function renderDots(){
      dotsContainer.innerHTML = IMAGENES.map((_,i)=>`<span class="dot${i===idx?' active':''}" data-i="${i}"></span>`).join('');
      dotsContainer.addEventListener("click", e=>{
        const el = e.target.closest(".dot"); if(!el) return;
        idx = +el.dataset.i;
        updateHero();
      });
    }
    renderDots();
    updateHero();
    setInterval(()=>{ idx=(idx+1)%IMAGENES.length; updateHero(); },5000);

    // MODO OSCURO
    const themeToggle = document.getElementById("theme-toggle");
    if(themeToggle){
      const body = document.body;
      if(localStorage.getItem("dark-mode")==="true") body.classList.add("dark-mode");
      themeToggle.setAttribute("aria-pressed", body.classList.contains("dark-mode"));
      themeToggle.addEventListener("click", ()=>{
        body.classList.toggle("dark-mode");
        const pressed = body.classList.contains("dark-mode");
        localStorage.setItem("dark-mode", pressed);
        themeToggle.setAttribute("aria-pressed", pressed);
      });
    }

    // SCROLL INFO
    const infoSection = document.querySelector(".info-institucion");
    if(infoSection){
      window.addEventListener("scroll", ()=>{
        const rect = infoSection.getBoundingClientRect();
        if(rect.top < window.innerHeight-100) infoSection.classList.add("visible");
      });
    }
    // SIDEBAR ADMIN
const esAdmin = document.getElementById("sidebar") !== null; // detecta si hay sidebar
if(esAdmin){
  const hamburger = document.getElementById("hamburger");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("overlay");

  if(hamburger && sidebar && overlay){
    // Abrir sidebar
    hamburger.addEventListener("click", ()=>{
      sidebar.classList.add("active");
      overlay.classList.add("active");
    });

    // Cerrar sidebar al click en overlay
    overlay.addEventListener("click", ()=>{
      sidebar.classList.remove("active");
      overlay.classList.remove("active");
    });
  }
}


  });