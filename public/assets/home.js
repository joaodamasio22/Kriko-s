const home = document.querySelector('.home-track');
const scene = home.querySelector('.home-scene');
const slogan = home.querySelector('.home-slogan');

home.classList.add('is-interactive');

// O botão que recebe o cursor ou foco traz só a sua própria imagem para frente.
for (const choice of home.querySelectorAll('.choice')) {
  const highlight = () => choice.classList.add('is-highlighted');
  const reset = () => choice.classList.remove('is-highlighted');

  choice.addEventListener('pointerenter', highlight);
  choice.addEventListener('pointerleave', reset);
  choice.addEventListener('focus', highlight);
  choice.addEventListener('blur', reset);
}

let pendingFrame = null;

// O progresso da rolagem move o logo e reduz a opacidade da frase.
function updateHome() {
  const distance = home.clientHeight - scene.clientHeight;
  const scrolled = -home.getBoundingClientRect().top;
  const progress = distance > 0 ? Math.max(0, Math.min(scrolled / distance, 1)) : 0;

  home.style.setProperty('--home-progress', progress);
  // A opacidade é aplicada na frase inteira, inclusive no trecho laranja.
  slogan.style.opacity = String(1 - progress);
  pendingFrame = null;
}

function scheduleUpdate() {
  if (pendingFrame === null) pendingFrame = requestAnimationFrame(updateHome);
}

addEventListener('scroll', scheduleUpdate, { passive: true });
addEventListener('resize', scheduleUpdate);
addEventListener('pageshow', scheduleUpdate);
updateHome();
