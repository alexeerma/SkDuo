/**
 * Theme entry point.
 * Replace Sage's default resources/js/app.js with this.
 */

import initAnimations from './animations.js';
import initProductArchive from './productArchive.js';

document.addEventListener('DOMContentLoaded', () => {
  initAnimations();
  initProductArchive();

  if (document.querySelector('[data-rally-canvas]')) {
    import('./rally3d.js').then(({ default: initRally3D }) => initRally3D());
  }

  if (document.querySelector('[data-trophy-canvas]')) {
    import('./trophy-shelf.js').then(({ default: initTrophyShelf }) => initTrophyShelf());
  }

  if (document.querySelector('[data-editorial-track]')) {
    import('./hero-editorial-test.js').then(({ default: initHeroEditorialTest }) => initHeroEditorialTest());
  }

  if (document.querySelector('[data-hero3d-accent-canvas]')) {
    import('./hero3d-accent.js').then(({ default: initHero3DAccent }) => initHero3DAccent());
  }

  if (document.querySelector('[data-bento-volleyball-canvas]')) {
    import('./bento-volleyball.js').then(({ default: initBentoVolleyball }) => initBentoVolleyball());
  }
});

// Hot module reload (Vite dev only).
if (import.meta.hot) {
  import.meta.hot.accept(() => {
    console.log('[skduo] HMR update');
  });
}
