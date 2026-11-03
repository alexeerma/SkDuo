/**
 * SK DUO animation library.
 * Imported from resources/js/app.js — no jQuery, no dependencies.
 *
 * Hooks (data attributes on any HTML element):
 *   [data-tilt]               3D mouse-follow tilt
 *   [data-magnet="0.3"]       Magnetic pull toward cursor (strength 0-1)
 *   [data-reveal]             Fade + slide-in on scroll
 *   [data-tick="123"]         Counter animation when visible
 *   [data-kinetic]            Word-by-word reveal headline
 *   [data-countdown-target]   Live countdown (Unix timestamp seconds)
 *   [data-training-modal]        Training sign-up modal (components.training-modal)
 *   [data-training-modal-open]   Any element that opens the training modal
 *   [data-training-modal-close]  Any element that closes the training modal
 *   [data-noored-slider]         Sidebar image slider, auto-advances every 4s (/noored Lisainfo card)
 *   [data-training-group-filter] Filters [data-training-group-card] by coach name (/noored)
 *   [data-training-group-card]   Smooth open/close animation, driven by data-state (/noored)
 */

// Coalesces a high-frequency handler (mousemove/pointermove) to at most one
// layout read + write per animation frame, instead of one per raw event.
const rafThrottle = (fn) => {
  let ticking = false;
  let lastArgs = null;
  return (...args) => {
    lastArgs = args;
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      fn(...lastArgs);
      ticking = false;
    });
  };
};

export default function initAnimations() {
  // Mobile menu scroll lock — functional, so it runs even under reduced motion.
  // `body:has(.skduo-mobile-menu[open])` in app.css covers browsers that support :has();
  // this class toggle is the fallback for browsers that don't.
  document.querySelectorAll('.skduo-mobile-menu').forEach((menu) => {
    menu.addEventListener('toggle', () => {
      document.body.classList.toggle('is-menu-open', menu.open);
    });
  });

  // Native <details> doesn't close on Escape by default.
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('.skduo-mobile-menu[open]').forEach((menu) => {
      menu.open = false;
      menu.querySelector('summary')?.focus();
    });
  });

  // Training sign-up modal — functional, so it runs even under reduced motion.
  // One modal per page; any [data-training-modal-open] button (hero CTA, /noored CTA, ...) opens it.
  const trainingModal = document.querySelector('[data-training-modal]');
  const trainingOpenButtons = document.querySelectorAll('[data-training-modal-open]');
  if (trainingModal && trainingOpenButtons.length) {
    const openTrainingModal = () => {
      trainingModal.classList.remove('hidden');
      trainingModal.classList.add('flex');
      trainingModal.setAttribute('aria-hidden', 'false');
      document.documentElement.classList.add('overflow-hidden');
    };
    const closeTrainingModal = () => {
      trainingModal.classList.add('hidden');
      trainingModal.classList.remove('flex');
      trainingModal.setAttribute('aria-hidden', 'true');
      document.documentElement.classList.remove('overflow-hidden');
    };
    trainingOpenButtons.forEach((button) => button.addEventListener('click', openTrainingModal));
    trainingModal.querySelectorAll('[data-training-modal-close]').forEach((button) => {
      button.addEventListener('click', closeTrainingModal);
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && trainingModal.getAttribute('aria-hidden') === 'false') closeTrainingModal();
    });
  }

  // Respect user motion preference.
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-visible'));
    document.querySelectorAll('[data-hero-intro]').forEach((el) => el.classList.add('is-hero-ready'));
    return;
  }

  // ===== 0. Hero photo spotlight =====
  document.querySelectorAll('[data-hero-spotlight]').forEach((hero) => {
    const mosaic = hero.querySelector('.skduo-hero-mosaic');
    if (!mosaic) return;

    const move = rafThrottle((e) => {
      const r = hero.getBoundingClientRect();
      mosaic.style.setProperty('--hero-x', `${e.clientX - r.left}px`);
      mosaic.style.setProperty('--hero-y', `${e.clientY - r.top}px`);
      hero.classList.add('is-active');
    });

    hero.addEventListener('pointermove', move);
    hero.addEventListener('pointerleave', () => hero.classList.remove('is-active'));
  });

  const startHeroIntro = () => {
    requestAnimationFrame(() => {
      document.querySelectorAll('[data-hero-intro]').forEach((hero) => hero.classList.add('is-hero-ready'));
    });
  };

  if (document.fonts && document.fonts.ready) {
    Promise.race([
      document.fonts.ready,
      new Promise((resolve) => setTimeout(resolve, 900)),
    ]).then(startHeroIntro);
  } else {
    startHeroIntro();
  }

  // ===== 0.5. Magic-bento style blog cards =====
  document.querySelectorAll('[data-bento-grid]').forEach((grid) => {
    const cards = [...grid.querySelectorAll('[data-bento-card]')];
    const isCoarsePointer = window.matchMedia('(hover: none), (pointer: coarse)').matches;

    if (!cards.length || isCoarsePointer) return;

    const updateCardGlow = (card, clientX, clientY, intensity = 1) => {
      const r = card.getBoundingClientRect();
      const x = clientX - r.left;
      const y = clientY - r.top;
      card.style.setProperty('--glow-x', `${x}px`);
      card.style.setProperty('--glow-y', `${y}px`);
      card.style.setProperty('--glow-intensity', String(Math.max(0, Math.min(intensity, 1))));
    };

    const resetCard = (card) => {
      card.style.setProperty('--glow-intensity', '0');
      card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translate3d(0, 0, 0)';
    };

    const makeParticles = (card) => {
      for (let i = 0; i < 10; i += 1) {
        const particle = document.createElement('span');
        particle.className = 'skduo-bento-particle';
        particle.style.setProperty('--x', `${18 + Math.random() * 64}%`);
        particle.style.setProperty('--y', `${18 + Math.random() * 64}%`);
        particle.style.setProperty('--tx', `${(Math.random() - 0.5) * 90}px`);
        particle.style.setProperty('--ty', `${(Math.random() - 0.75) * 110}px`);
        particle.style.setProperty('--size', `${3 + Math.random() * 5}px`);
        particle.style.animationDelay = `${i * 28}ms`;
        card.appendChild(particle);
        particle.addEventListener('animationend', () => particle.remove(), { once: true });
      }
    };

    grid.addEventListener('pointermove', rafThrottle((e) => {
      cards.forEach((card) => {
        const r = card.getBoundingClientRect();
        const centerX = r.left + r.width / 2;
        const centerY = r.top + r.height / 2;
        const distance = Math.hypot(e.clientX - centerX, e.clientY - centerY);
        const fadeDistance = Math.max(r.width, r.height) * 1.15;
        const intensity = Math.max(0, 1 - distance / fadeDistance);
        updateCardGlow(card, e.clientX, e.clientY, intensity);
      });
    }), { passive: true });

    grid.addEventListener('pointerleave', () => cards.forEach(resetCard));

    cards.forEach((card) => {
      let particleCooldown = false;
      card.addEventListener('pointerenter', () => {
        if (particleCooldown) return;
        particleCooldown = true;
        makeParticles(card);
        setTimeout(() => { particleCooldown = false; }, 400);
      });

      card.addEventListener('pointermove', rafThrottle((e) => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width;
        const y = (e.clientY - r.top) / r.height;
        const rotateX = (y - 0.5) * -8;
        const rotateY = (x - 0.5) * 8;
        const magnetX = (x - 0.5) * 8;
        const magnetY = (y - 0.5) * 8;

        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translate3d(${magnetX}px, ${magnetY}px, 0)`;
      }), { passive: true });

      card.addEventListener('pointerleave', () => resetCard(card));

      card.addEventListener('click', (e) => {
        const r = card.getBoundingClientRect();
        const maxDistance = Math.max(
          Math.hypot(e.clientX - r.left, e.clientY - r.top),
          Math.hypot(e.clientX - r.right, e.clientY - r.top),
          Math.hypot(e.clientX - r.left, e.clientY - r.bottom),
          Math.hypot(e.clientX - r.right, e.clientY - r.bottom),
        );
        const ripple = document.createElement('span');
        ripple.className = 'skduo-bento-ripple';
        ripple.style.setProperty('--x', `${e.clientX - r.left}px`);
        ripple.style.setProperty('--y', `${e.clientY - r.top}px`);
        ripple.style.setProperty('--size', `${maxDistance * 2}px`);
        card.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
      });
    });
  });

  // ===== 1. Scroll progress bar (sits under the sticky nav, fills left → right) =====
  const bar = document.createElement('div');
  bar.className = 'skduo-scroll-progress';
  document.body.appendChild(bar);

  const siteNav = document.querySelector('[data-site-nav]');
  const syncHeaderHeight = () => {
    const height = siteNav ? siteNav.getBoundingClientRect().height : 0;
    document.documentElement.style.setProperty('--skduo-header-height', `${height}px`);
  };
  syncHeaderHeight();
  window.addEventListener('resize', syncHeaderHeight, { passive: true });

  let progressTicking = false;
  const updateProgressBar = () => {
    const h = document.documentElement;
    const pct = h.scrollTop / (h.scrollHeight - h.clientHeight);
    bar.style.transform = `scaleX(${pct})`;
    progressTicking = false;
  };
  window.addEventListener('scroll', () => {
    if (progressTicking) return;
    progressTicking = true;
    requestAnimationFrame(updateProgressBar);
  }, { passive: true });

  // ===== 2. 3D tilt =====
  document.querySelectorAll('[data-tilt]').forEach((card) => {
    card.addEventListener('mousemove', rafThrottle((e) => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width;
      const y = (e.clientY - r.top) / r.height;
      card.style.transform = `perspective(1000px) rotateX(${(y - 0.5) * -12}deg) rotateY(${(x - 0.5) * 12}deg) translateZ(10px)`;
      card.style.setProperty('--mx', `${x * 100}%`);
      card.style.setProperty('--my', `${y * 100}%`);
    }));
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
    });
  });

  // ===== 3. Magnetic buttons =====
  document.querySelectorAll('[data-magnet]').forEach((btn) => {
    const strength = parseFloat(btn.dataset.magnet) || 0.3;
    btn.addEventListener('mousemove', rafThrottle((e) => {
      const r = btn.getBoundingClientRect();
      const x = e.clientX - r.left - r.width / 2;
      const y = e.clientY - r.top - r.height / 2;
      btn.style.transform = `translate(${x * strength}px, ${y * strength}px)`;
    }));
    btn.addEventListener('mouseleave', () => {
      btn.style.transform = 'translate(0, 0)';
    });
  });

  // ===== 4. Reveal on scroll =====
  const revealIO = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('is-visible'), i * 80);
        revealIO.unobserve(e.target);
      }
    });
  }, { threshold: 0.15 });
  document.querySelectorAll('[data-reveal]').forEach((el) => revealIO.observe(el));

  // ===== 5. Number tickers =====
  const tickIO = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (!e.isIntersecting) return;
      const el = e.target;
      const target = parseFloat(el.dataset.tick);
      const suffix = el.dataset.suffix || '';
      const duration = parseInt(el.dataset.duration, 10) || 1600;
      const start = performance.now();
      const step = (now) => {
        const p = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        const v = target * eased;
        el.textContent = (target % 1 ? v.toFixed(1) : Math.floor(v).toLocaleString('et-EE')) + (p === 1 ? suffix : '');
        if (p < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
      tickIO.unobserve(el);
    });
  }, { threshold: 0.3 });
  document.querySelectorAll('[data-tick]').forEach((el) => tickIO.observe(el));

  // ===== 6. Kinetic headline =====
  document.querySelectorAll('[data-kinetic]').forEach((h) => {
    const words = h.textContent.trim().split(/\s+/);
    h.innerHTML = words.map((w) => `<span class="kw">${w}</span>`).join(' ');
    const spans = h.querySelectorAll('.kw');
    const io = new IntersectionObserver((es) => {
      es.forEach((e) => {
        if (!e.isIntersecting) return;
        spans.forEach((s, i) => setTimeout(() => s.classList.add('in'), i * 120));
        io.unobserve(e.target);
      });
    }, { threshold: 0.4 });
    io.observe(h);
  });

  // ===== 7. Live countdown =====
  document.querySelectorAll('[data-countdown-target]').forEach((wrap) => {
    const target = parseInt(wrap.dataset.countdownTarget, 10) * 1000;
    const els = {
      d: wrap.querySelector('[data-cd="d"]'),
      h: wrap.querySelector('[data-cd="h"]'),
      m: wrap.querySelector('[data-cd="m"]'),
      s: wrap.querySelector('[data-cd="s"]'),
    };
    const tick = () => {
      const diff = target - Date.now();
      if (diff <= 0) { wrap.classList.add('is-live'); return; }
      const dd = Math.floor(diff / 86400000);
      const hh = Math.floor((diff % 86400000) / 3600000);
      const mm = Math.floor((diff % 3600000) / 60000);
      const ss = Math.floor((diff % 60000) / 1000);
      if (els.d) els.d.textContent = String(dd).padStart(2, '0');
      if (els.h) els.h.textContent = String(hh).padStart(2, '0');
      if (els.m) els.m.textContent = String(mm).padStart(2, '0');
      if (els.s) els.s.textContent = String(ss).padStart(2, '0');
    };
    tick();
    setInterval(tick, 1000);
  });

  // ===== 8. Noored sidebar image slider (auto-advance) =====
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('[data-noored-slider]').forEach((slider) => {
    const track = slider.querySelector('[data-noored-slider-track]');
    const slides = track ? track.children.length : 0;
    if (!track || slides < 2 || prefersReducedMotion) return;

    let index = 0;
    setInterval(() => {
      index = (index + 1) % slides;
      track.scrollTo({ left: index * track.clientWidth, behavior: 'smooth' });
    }, 4000);
  });

  // ===== 9. Training group coach filter (/noored) =====
  document.querySelectorAll('[data-training-group-filter]').forEach((input) => {
    const list = input.closest('section')?.querySelector('[data-training-group-list]');
    const empty = input.closest('section')?.querySelector('[data-training-group-empty]');
    const cards = list ? Array.from(list.querySelectorAll('[data-training-group-card]')) : [];
    if (!cards.length) return;

    input.addEventListener('input', () => {
      const query = input.value.trim().toLowerCase();
      let visibleCount = 0;

      cards.forEach((card) => {
        const isMatch = !query || (card.dataset.coachName || '').includes(query);
        card.classList.toggle('hidden', !isMatch);
        if (isMatch) visibleCount += 1;
      });

      if (empty) empty.classList.toggle('hidden', visibleCount !== 0);
    });
  });

  // ===== 10. Training group card smooth open/close (/noored) =====
  // `data-state` (not the native `open` attribute) drives all visual transitions
  // (icon rotation, border/shadow) so they stay in sync with the JS height
  // animation instead of snapping in with the native, un-animatable toggle.
  const prefersReducedMotionDetails = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  document.querySelectorAll('[data-training-group-card]').forEach((card) => {
    const summary = card.querySelector('summary');
    const body = card.querySelector('[data-training-group-body]');
    if (!summary || !body) return;

    // Reduced motion: keep the native instant toggle, just sync data-state for styling.
    if (prefersReducedMotionDetails) {
      card.addEventListener('toggle', () => {
        card.dataset.state = card.open ? 'open' : 'closed';
      });
      return;
    }

    let animation = null;

    summary.addEventListener('click', (event) => {
      event.preventDefault();
      if (animation) return;

      if (card.open) {
        const startHeight = body.offsetHeight;
        card.dataset.state = 'closed';
        animation = body.animate(
          { height: [`${startHeight}px`, '0px'] },
          { duration: 260, easing: 'cubic-bezier(0.4, 0, 0.2, 1)' }
        );
        animation.onfinish = () => {
          card.open = false;
          body.style.height = '';
          animation = null;
        };
      } else {
        card.open = true;
        card.dataset.state = 'open';
        const endHeight = body.scrollHeight;
        animation = body.animate(
          { height: ['0px', `${endHeight}px`] },
          { duration: 260, easing: 'cubic-bezier(0.4, 0, 0.2, 1)' }
        );
        animation.onfinish = () => {
          body.style.height = '';
          animation = null;
        };
      }
    });
  });
}
