const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));
const lerp = (a, b, t) => a + (b - a) * t;
const smoothstep = (t) => t * t * (3 - 2 * t);

/**
 * landonorris.com-style pinned intro: a huge headline shrinks and fades as
 * you scroll through it, revealing a full-bleed photo underneath. Same
 * scroll-scrubbed, settle-and-stop rAF loop as rally3d.js/trophy-shelf.js —
 * just driving CSS transforms instead of a Three.js scene.
 */
function initEditorialStage() {
  const track = document.querySelector('[data-editorial-track]');
  if (!track) return;

  const headline = document.querySelector('[data-editorial-headline]');
  const kicker = document.querySelector('[data-editorial-kicker]');
  const photo = document.querySelector('[data-editorial-photo]');
  const cue = document.querySelector('[data-editorial-cue]');

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let progress = 0;
  let targetProgress = 0;
  let rafId = null;

  const renderFrame = () => {
    if (headline) {
      const shrink = clamp(progress / 0.85);
      headline.style.transform = `translateY(${lerp(0, -64, shrink)}px) scale(${lerp(1, 0.55, shrink)})`;
      headline.style.opacity = String(1 - smoothstep(clamp((progress - 0.5) / 0.4)));
    }
    if (kicker) kicker.style.opacity = String(1 - clamp(progress / 0.25));
    if (photo) {
      const reveal = smoothstep(clamp((progress - 0.2) / 0.65));
      photo.style.opacity = String(reveal);
      photo.style.transform = `scale(${lerp(1.2, 1, reveal)})`;
    }
    if (cue) cue.style.opacity = String(progress < 0.06 ? 1 : 0);
  };

  const render = () => {
    const previous = progress;
    progress += (targetProgress - progress) * 0.09;
    renderFrame();

    if (Math.abs(targetProgress - progress) < 0.0005 && Math.abs(progress - previous) < 0.0005) {
      rafId = null;
      return;
    }
    rafId = requestAnimationFrame(render);
  };

  const startRender = () => { if (rafId === null) rafId = requestAnimationFrame(render); };
  const stopRender = () => {
    if (rafId !== null) cancelAnimationFrame(rafId);
    rafId = null;
  };

  const updateProgress = () => {
    const rect = track.getBoundingClientRect();
    targetProgress = clamp(-rect.top / Math.max(1, rect.height - window.innerHeight));
  };

  let inViewport = false;
  const visibilityObserver = new IntersectionObserver(([entry]) => {
    inViewport = entry.isIntersecting;
    if (inViewport && document.visibilityState === 'visible') startRender();
    else stopRender();
  }, { threshold: 0 });
  visibilityObserver.observe(track);

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && inViewport) startRender();
    else stopRender();
  });

  if (prefersReducedMotion) {
    progress = 0;
    targetProgress = 0;
    renderFrame();
    if (headline) headline.style.opacity = '1';
    if (kicker) kicker.style.opacity = '1';
    if (photo) { photo.style.opacity = '0.55'; photo.style.transform = 'scale(1)'; }
    return;
  }

  window.addEventListener('scroll', () => {
    updateProgress();
    if (inViewport) startRender();
  }, { passive: true });
  updateProgress();
  renderFrame();
}

/**
 * Horizontal scroll-rail (à la landonorris.com's helmet gallery): a pinned
 * row of cards translated sideways as the page scrolls vertically through
 * a tall track. Falls back to a normal horizontally-scrollable row under
 * prefers-reduced-motion (handled by the template's own <style> block).
 */
function initEditorialRail() {
  const track = document.querySelector('[data-rail-track]');
  const inner = document.querySelector('[data-rail-inner]');
  if (!track || !inner) return;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  let maxTranslate = 0;
  let progress = 0;
  let targetProgress = 0;
  let rafId = null;

  const resize = () => {
    maxTranslate = Math.max(0, inner.scrollWidth - window.innerWidth);
  };

  const updateProgress = () => {
    const rect = track.getBoundingClientRect();
    targetProgress = clamp(-rect.top / Math.max(1, rect.height - window.innerHeight));
  };

  const renderFrame = () => {
    inner.style.transform = `translate3d(${-progress * maxTranslate}px, 0, 0)`;
  };

  const render = () => {
    const previous = progress;
    progress += (targetProgress - progress) * 0.12;
    renderFrame();

    if (Math.abs(targetProgress - progress) < 0.0005 && Math.abs(progress - previous) < 0.0005) {
      rafId = null;
      return;
    }
    rafId = requestAnimationFrame(render);
  };

  const startRender = () => { if (rafId === null) rafId = requestAnimationFrame(render); };

  let inViewport = false;
  const visibilityObserver = new IntersectionObserver(([entry]) => {
    inViewport = entry.isIntersecting;
    if (inViewport) startRender();
  }, { threshold: 0 });
  visibilityObserver.observe(track);

  window.addEventListener('resize', () => { resize(); updateProgress(); renderFrame(); });
  window.addEventListener('scroll', () => {
    updateProgress();
    if (inViewport) startRender();
  }, { passive: true });

  resize();
  updateProgress();
  renderFrame();
}

export default function initHeroEditorialTest() {
  initEditorialStage();
  initEditorialRail();
}
