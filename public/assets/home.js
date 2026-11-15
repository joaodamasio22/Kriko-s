const home = document.querySelector('.home-track');
const scene = home.querySelector('.home-scene');
const slogan = home.querySelector('.home-slogan');

home.classList.add('is-interactive');

for (const choice of home.querySelectorAll('.choice')) {
    const highlight = () => choice.classList.add('is-highlighted');
    const reset = () => choice.classList.remove('is-highlighted');

    choice.addEventListener('pointerenter', highlight);
    choice.addEventListener('pointerleave', reset);
    choice.addEventListener('focus', highlight);
    choice.addEventListener('blur', reset);
}

let pendingFrame = null;

function updateHome() {
    const distance = home.clientHeight - scene.clientHeight;
    const scrolled = -home.getBoundingClientRect().top;
    const progress = distance > 0 ? Math.max(0, Math.min(scrolled / distance, 1)) : 0;

    home.style.setProperty('--home-progress', progress);
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
