export default function initProductArchive() {
  const parser = new DOMParser();

  async function loadArchive(url, shouldPushState = true, fromPagination = false) {
    const archive = document.querySelector('[data-product-archive]');
    const hero = document.querySelector('[data-product-archive-hero]');

    if (!archive || !hero) {
      window.location.href = url;
      return;
    }

    archive.setAttribute('aria-busy', 'true');
    archive.classList.add('pointer-events-none', 'opacity-60');

    try {
      const response = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (!response.ok) {
        throw new Error(`Archive request failed: ${response.status}`);
      }

      const html = await response.text();
      const doc = parser.parseFromString(html, 'text/html');
      const nextArchive = doc.querySelector('[data-product-archive]');
      const nextHero = doc.querySelector('[data-product-archive-hero]');

      if (!nextArchive || !nextHero) {
        throw new Error('Archive markup missing from response.');
      }

      hero.replaceWith(nextHero);
      archive.replaceWith(nextArchive);
      document.title = doc.title;

      if (shouldPushState) {
        window.history.pushState({ productArchiveUrl: url }, '', url);
      }

      // The clicked link was replaced, so focus would fall back to <body>. Move it to the
      // now-active filter pill, or to the product list after a pagination click.
      if (shouldPushState) {
        const activePill = document.querySelector('[data-product-archive-link][aria-current="page"]');
        const target = (!fromPagination && activePill) || document.querySelector('[data-product-archive]');
        if (target) {
          if (target.matches('[data-product-archive]')) target.setAttribute('tabindex', '-1');
          target.focus();
        }
      }

    } catch (error) {
      window.location.href = url;
    }
  }

  document.addEventListener('click', (event) => {
    const link = event.target.closest('[data-product-archive-link], [data-product-pagination] a');

    if (!link) {
      return;
    }

    const url = new URL(link.href, window.location.href);

    if (url.origin !== window.location.origin) {
      return;
    }

    event.preventDefault();
    loadArchive(url.href, true, Boolean(link.closest('[data-product-pagination]')));
  });

  window.addEventListener('popstate', () => {
    if (document.querySelector('[data-product-archive]')) {
      loadArchive(window.location.href, false);
    }
  });
}
