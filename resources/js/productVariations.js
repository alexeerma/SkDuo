/**
 * Variable products: render each attribute <select> as a row of pill buttons.
 * The native select stays in the DOM (hidden) and remains the source of truth,
 * so WooCommerce's add-to-cart-variation.js keeps working unchanged.
 */
export default function initProductVariations() {
  document.querySelectorAll('form.variations_form').forEach((form) => {
    const selects = [...form.querySelectorAll('table.variations select')];
    const $ = window.jQuery;

    const groups = selects.map((select) => {
      const options = [...select.options].filter((option) => option.value !== '');

      if (options.length === 0 || options.length > 16) {
        return null;
      }

      const labelEl = form.querySelector(`label[for="${select.id}"]`);
      const group = document.createElement('div');
      group.className = 'skduo-variation-pills';
      group.setAttribute('role', 'group');
      if (labelEl) {
        labelEl.id = labelEl.id || `${select.id}-label`;
        group.setAttribute('aria-labelledby', labelEl.id);
      }

      const buttons = options.map((option) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'skduo-variation-pill';
        button.textContent = option.textContent.trim();
        button.dataset.value = option.value;
        button.addEventListener('click', () => {
          const next = select.value === option.value ? '' : option.value;
          select.value = next;
          select.dispatchEvent(new Event('change', { bubbles: true }));
          sync();
        });
        group.appendChild(button);
        return button;
      });

      select.classList.add('skduo-variation-select');
      select.setAttribute('tabindex', '-1');
      select.setAttribute('aria-hidden', 'true');
      select.insertAdjacentElement('afterend', group);

      return { select, buttons };
    }).filter(Boolean);

    function sync() {
      groups.forEach(({ select, buttons }) => {
        buttons.forEach((button) => {
          const option = [...select.options].find((item) => item.value === button.dataset.value);
          const available = Boolean(option) && !option.disabled;
          button.setAttribute('aria-pressed', select.value === button.dataset.value ? 'true' : 'false');
          button.disabled = !available;
        });
      });
    }

    sync();

    if ($) {
      const $form = $(form);
      $form.on('woocommerce_update_variation_values reset_data', () => window.requestAnimationFrame(sync));

      // Swap the custom gallery's main image to the chosen variation's image.
      const main = document.querySelector('[data-product-gallery-main]');
      if (main) {
        $form.on('found_variation', (event, variation) => {
          if (variation && variation.image && variation.image.full_src) {
            main.src = variation.image.src || variation.image.full_src;
            main.alt = variation.image.alt || main.dataset.defaultAlt || '';
          }
        });
        $form.on('reset_image', () => {
          main.src = main.dataset.defaultSrc;
          main.alt = main.dataset.defaultAlt || '';
        });
      }
    } else {
      selects.forEach((select) => select.addEventListener('change', () => window.requestAnimationFrame(sync)));
    }
  });
}
