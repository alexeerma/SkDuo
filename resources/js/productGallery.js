export default function initProductGallery() {
  document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-product-gallery-main]');
    const thumbs = [...gallery.querySelectorAll('[data-product-gallery-thumb]')];

    if (!main || thumbs.length < 2) {
      return;
    }

    thumbs.forEach((thumb) => {
      thumb.addEventListener('click', () => {
        main.src = thumb.dataset.full;
        main.alt = thumb.dataset.alt || '';
        thumbs.forEach((other) => {
          other.setAttribute('aria-pressed', other === thumb ? 'true' : 'false');
        });
      });
    });
  });
}
